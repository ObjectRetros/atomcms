<?php

namespace App\Services\User;

use App\Models\Game\Guild\GuildMember;
use App\Models\Game\Player\MessengerFriendship;
use App\Models\User;

class UserProfileService
{
    public function getProfileData(User $user): array
    {
        return [
            'user' => $this->loadUserRelations($user),
            'friends' => $this->getUserFriends($user->id),
            'groups' => $this->getUserGroups($user->id),
            'guestbook' => $user->profileGuestbook()->with('user')->latest()->limit(5)->get(),
            'photos' => $user->photos()->limit(3)->get(),
        ];
    }

    public function loadUserRelations(User $user): User
    {
        return $user->load([
            'badges' => function ($badges) {
                $badges->where('slot_id', '>', '0')
                    ->orderBy('slot_id')
                    ->take(5);
            },
            'rooms' => function ($rooms) {
                $rooms->select('id', 'owner_id', 'name', 'users')
                    ->orderByDesc('users')
                    ->orderBy('id');
            },
        ]);
    }

    public function getUserFriends(int $userId)
    {
        return MessengerFriendship::select('user_two_id')
            ->where('user_one_id', '=', $userId)
            ->whereHas('user')
            ->with('user:id,username,look')
            ->inRandomOrder()
            ->take(12)
            ->get();
    }

    public function getUserGroups(int $userId)
    {
        return GuildMember::query()
            ->select(['guilds_members.id', 'guilds_members.guild_id', 'guilds_members.user_id', 'guilds.name', 'guilds.badge'])
            ->where('guilds_members.user_id', '=', $userId)
            ->join('guilds', 'guilds_members.guild_id', '=', 'guilds.id')
            ->inRandomOrder()
            ->take(6)
            ->get();
    }
}
