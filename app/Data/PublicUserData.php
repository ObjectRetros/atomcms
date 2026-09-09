<?php

namespace App\Data;

use App\Models\User;

final readonly class PublicUserData
{
    public const COLUMNS = ['id', 'username', 'motto', 'look', 'online'];

    public function __construct(public int $id, public string $username, public string $motto, public string $look, public bool $online) {}

    public static function from(User $user): self
    {
        return new self((int) $user->id, $user->username, (string) $user->motto, $user->look, (bool) $user->online);
    }
}
