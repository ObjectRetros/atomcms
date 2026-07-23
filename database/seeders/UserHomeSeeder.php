<?php

namespace Database\Seeders;

use App\Actions\Home\CreateDefaultHome;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserHomeSeeder extends Seeder
{
    /**
     * Give every user a default home (background + My Profile widget).
     * Users who already own these items are left untouched, so the
     * seeder is safe to re-run on existing hotels.
     */
    public function run(CreateDefaultHome $defaultHome): void
    {
        User::query()->chunkById(100, function ($users) use ($defaultHome) {
            foreach ($users as $user) {
                $defaultHome->execute($user);
            }
        });
    }
}
