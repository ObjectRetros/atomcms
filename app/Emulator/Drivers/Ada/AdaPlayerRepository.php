<?php

namespace App\Emulator\Drivers\Ada;

use App\Emulator\Contracts\PlayerRepository;
use App\Emulator\Data\HomeFriend;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Ada normalises player state across several EF-owned tables. Atom keeps a
 * compatibility users row so its own foreign keys stay valid: CMS writes flow
 * outwards through created()/updated(), and reads are refreshed from Ada by
 * hydrateMany() as models come off the query builder.
 *
 * The mirrored columns are only authoritative for CMS-owned data. Anything Ada
 * writes during gameplay - online state, balances, motto, look - has to be
 * queried through this driver rather than through the users table.
 */
class AdaPlayerRepository implements PlayerRepository
{
    /** The status Ada gives an accepted friendship. */
    private const FRIENDSHIP_ACCEPTED = 2;

    /** Ada stores a friendship once, so either column can hold the friend. */
    private const FRIEND_ID = 'CASE WHEN origin_player_id = ? THEN target_player_id ELSE origin_player_id END AS friend_id';

    /** Player tables Ada declares ON DELETE RESTRICT rather than CASCADE. */
    private const RESTRICTED_TABLES = ['player_tags', 'player_wardrobe_items'];

    /** Ada columns hydrateMany() reads back into the compatibility row. */
    private const HYDRATED_COLUMNS = [
        'players.id',
        'players.username',
        'players.email',
        'players.password',
        'players.created_at',
        'player_avatar_data.figure_code',
        'player_avatar_data.motto',
        'player_avatar_data.gender',
        'player_data.home_room_id',
        'player_data.credit_balance',
        'player_data.pixel_balance',
        'player_data.gotw_points',
        'player_data.is_online',
        'player_data.last_online',
        'player_website_data.initial_ip',
        'player_website_data.last_ip',
        'player_website_data.last_login',
    ];

    public function created(User $user): void
    {
        DB::transaction(function () use ($user): void {
            if (DB::table('players')->where('id', $user->id)->exists()) {
                $this->synchronize($user);

                return;
            }

            $createdAt = now();

            DB::table('players')->insert([
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->mail,
                'password' => $user->password,
                'created_at' => $createdAt,
            ]);

            DB::table('player_avatar_data')->insert([
                'player_id' => $user->id,
                'figure_code' => $user->look,
                'motto' => $user->motto,
                'gender' => $user->gender ?: 'M',
                'chat_bubble_id' => 0,
            ]);

            DB::table('player_data')->insert([
                'player_id' => $user->id,
                'home_room_id' => $user->home_room ?: null,
                'credit_balance' => (int) $user->credits,
                'pixel_balance' => 0,
                'seasonal_balance' => 0,
                'gotw_points' => 0,
                'respect_points' => 15,
                'respect_points_pet' => 15,
                'achievement_score' => 15,
                'allow_friend_requests' => true,
                'is_online' => false,
                'last_online' => null,
            ]);

            DB::table('player_game_settings')->insert([
                'player_id' => $user->id,
                'system_volume' => 100,
                'furniture_volume' => 100,
                'trax_volume' => 100,
                'prefer_old_chat' => false,
                'block_room_invites' => false,
                'block_camera_follow' => false,
                'ui_flags' => 1,
                'show_notifications' => true,
            ]);

            DB::table('player_navigator_settings')->insert([
                'player_id' => $user->id,
                'window_x' => 50,
                'window_y' => 50,
                'window_width' => 435,
                'window_height' => 535,
                'open_searches' => false,
            ]);

            DB::table('player_website_data')->insert([
                'player_id' => $user->id,
                'initial_ip' => $user->ip_register,
                'last_ip' => $user->ip_current,
                'last_login' => $createdAt,
            ]);

            $this->assignRole($user);
        });
    }

    public function updated(User $user): void
    {
        if (! DB::table('players')->where('id', $user->id)->exists()) {
            $this->created($user);

            return;
        }

        // Users are saved for plenty of CMS-only reasons - login timestamps,
        // website balance, referrals - so only push aggregates that changed.
        $changes = array_filter([
            'players' => $this->changes($user, ['username' => 'username', 'mail' => 'email', 'password' => 'password']),
            'player_avatar_data' => $this->changes($user, ['look' => 'figure_code', 'motto' => 'motto', 'gender' => 'gender']),
            'player_data' => $this->changes($user, ['home_room' => 'home_room_id']),
            'player_website_data' => $this->changes($user, ['ip_current' => 'last_ip', 'last_login' => 'last_login']),
        ]);

        $rankChanged = $user->wasChanged('rank');

        if ($changes === [] && ! $rankChanged) {
            return;
        }

        DB::transaction(function () use ($user, $changes, $rankChanged): void {
            foreach ($changes as $table => $values) {
                DB::table($table)
                    ->where($table === 'players' ? 'id' : 'player_id', $user->id)
                    ->update($values);
            }

            if ($rankChanged) {
                DB::table('player_role')->where('player_id', $user->id)->delete();
                $this->assignRole($user);
            }
        });
    }

    public function deleted(User $user): void
    {
        DB::transaction(function () use ($user): void {
            // Ada cascades almost every player table, but restricts these two,
            // so deleting an account that ever saved an outfit or a tag would
            // fail on a foreign key. Clear them first; orphaned rows are junk.
            foreach (self::RESTRICTED_TABLES as $table) {
                DB::table($table)->where('player_id', $user->id)->delete();
            }

            DB::table('players')->where('id', $user->id)->delete();
        });
    }

    public function hydrateMany(array $users): void
    {
        $byId = [];

        foreach ($users as $user) {
            // A query that did not select the key has nothing to match on.
            if ($user->getKey() !== null) {
                $byId[(int) $user->getKey()] = $user;
            }
        }

        if ($byId === []) {
            return;
        }

        $ids = array_keys($byId);

        $players = DB::table('players')
            ->leftJoin('player_avatar_data', 'player_avatar_data.player_id', '=', 'players.id')
            ->leftJoin('player_data', 'player_data.player_id', '=', 'players.id')
            ->leftJoin('player_website_data', 'player_website_data.player_id', '=', 'players.id')
            ->whereIn('players.id', $ids)
            ->get(self::HYDRATED_COLUMNS)
            ->keyBy('id');

        $roleIds = DB::table('player_role')
            ->whereIn('player_id', $ids)
            ->selectRaw('player_id, MAX(role_id) as role_id')
            ->groupBy('player_id')
            ->pluck('role_id', 'player_id');

        foreach ($byId as $id => $user) {
            $player = $players->get($id);

            if ($player !== null) {
                $this->apply($user, $player, (int) ($roleIds[$id] ?? 1));
            }
        }
    }

    public function whereOnline(Builder $query): Builder
    {
        return $query->whereIn(
            $query->getModel()->getQualifiedKeyName(),
            DB::table('player_data')->select('player_id')->where('is_online', true),
        );
    }

    public function issueSso(User $user): string
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $token = sprintf('%s-%s', Str::replace(' ', '', setting('hotel_name', 'Atom')), Str::uuid());

            if (DB::table('player_sso_tokens')->where('token', $token)->exists()) {
                continue;
            }

            DB::table('player_sso_tokens')->insert([
                'player_id' => $user->id,
                'token' => $token,
                'created_at' => now(),
                'expires_at' => now()->addMinutes(5),
                'used_at' => null,
            ]);

            return $token;
        }

        throw new RuntimeException('Failed to generate unique Ada SSO token after 5 attempts.');
    }

    public function onlineFriends(User $user, int $limit): Collection
    {
        // Presence and recency both live on player_data; users.last_online is
        // only the mirror and is not written back, so ordering by it here
        // would sort on whatever the compatibility import last left behind.
        return User::query()
            ->whereKey($this->friendIds($user))
            ->join('player_data', 'player_data.player_id', '=', 'users.id')
            ->where('player_data.is_online', true)
            ->orderByDesc('player_data.last_online')
            ->limit($limit)
            ->get(['users.id', 'users.username', 'users.look', 'users.motto', 'users.last_online']);
    }

    /** @return LengthAwarePaginator<int, HomeFriend> */
    public function friendsForHome(User $user, int $perPage, string $pageName): LengthAwarePaginator
    {
        $paginator = $this->friendships($user)
            ->selectRaw(self::FRIEND_ID . ', id', [$user->id])
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], $pageName);

        $users = User::query()
            ->whereKey($paginator->getCollection()->pluck('friend_id'))
            ->get(['id', 'username', 'look', 'online'])
            ->keyBy('id');

        $friends = $paginator->getCollection()->map(function (object $friendship) use ($users): HomeFriend {
            $friendId = (int) data_get($friendship, 'friend_id');

            return new HomeFriend($users->get($friendId));
        })->values();

        return new LengthAwarePaginator($friends, $paginator->total(), $perPage, $paginator->currentPage(), [
            'path' => $paginator->path(),
            'pageName' => $pageName,
        ]);
    }

    /**
     * Ada records a friendship once, from whichever side sent the request.
     */
    private function friendships(User $user): QueryBuilder
    {
        return DB::table('player_friendships')
            ->where('status', self::FRIENDSHIP_ACCEPTED)
            ->where(fn ($query) => $query
                ->where('origin_player_id', $user->id)
                ->orWhere('target_player_id', $user->id));
    }

    /** @return Collection<int, int> */
    private function friendIds(User $user): Collection
    {
        return $this->friendships($user)
            ->selectRaw(self::FRIEND_ID, [$user->id])
            ->pluck('friend_id')
            ->unique()
            ->values();
    }

    /**
     * The subset of an aggregate's columns whose backing attribute changed.
     *
     * @param  array<string, string>  $map  users attribute => Ada column
     *
     * @return array<string, mixed>
     */
    private function changes(User $user, array $map): array
    {
        $changes = [];

        foreach ($map as $attribute => $column) {
            if (! $user->wasChanged($attribute)) {
                continue;
            }

            $changes[$column] = match ($column) {
                'gender' => $user->gender ?: 'M',
                'home_room_id' => $user->home_room ?: null,
                'last_login' => $this->dateTime($user->last_login),
                default => $user->getAttribute($attribute),
            };
        }

        return $changes;
    }

    private function apply(User $user, object $player, int $rank): void
    {
        $user->setRawAttributes(array_merge($user->getAttributes(), [
            'username' => data_get($player, 'username'),
            'mail' => data_get($player, 'email'),
            'password' => data_get($player, 'password'),
            'account_created' => $this->unix(data_get($player, 'created_at')),
            'last_online' => $this->unix(data_get($player, 'last_online')),
            'last_login' => $this->unix(data_get($player, 'last_login')),
            'motto' => data_get($player, 'motto') ?? '',
            'look' => data_get($player, 'figure_code') ?? '',
            'gender' => data_get($player, 'gender') ?? 'M',
            'rank' => $rank,
            'credits' => (int) data_get($player, 'credit_balance'),
            'pixels' => (int) data_get($player, 'pixel_balance'),
            'points' => (int) data_get($player, 'gotw_points'),
            'online' => (bool) data_get($player, 'is_online'),
            'ip_register' => data_get($player, 'initial_ip') ?? $user->ip_register,
            'ip_current' => data_get($player, 'last_ip') ?? $user->ip_current,
            'home_room' => (int) data_get($player, 'home_room_id'),
        ]), true);
    }

    private function synchronize(User $user): void
    {
        DB::table('players')->where('id', $user->id)->update([
            'username' => $user->username,
            'email' => $user->mail,
            'password' => $user->password,
        ]);
    }

    private function dateTime(mixed $value): Carbon
    {
        return is_numeric($value) && (int) $value > 0
            ? Carbon::createFromTimestamp((int) $value)
            : now();
    }

    private function unix(mixed $value): int
    {
        return $value === null ? 0 : Carbon::parse($value)->unix();
    }

    /**
     * Give the player the highest role at or below their CMS rank.
     *
     * player_role.role_id is a real foreign key, so a hotel that has not
     * seeded any roles yet gets no pivot row rather than a dangling one.
     */
    private function assignRole(User $user): void
    {
        $roleId = $this->roleIdForRank((int) $user->rank);

        if ($roleId !== null) {
            DB::table('player_role')->insert([
                'player_id' => $user->id,
                'role_id' => $roleId,
            ]);
        }
    }

    private function roleIdForRank(int $rank): ?int
    {
        $roleId = DB::table('roles')
            ->where('id', '<=', max(1, $rank))
            ->orderByDesc('id')
            ->value('id') ?? DB::table('roles')->min('id');

        return $roleId === null ? null : (int) $roleId;
    }
}
