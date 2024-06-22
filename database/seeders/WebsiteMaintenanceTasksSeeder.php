<?php

namespace Database\Seeders;

use App\Models\Game\Permission;
use App\Models\Miscellaneous\WebsiteMaintenanceTask;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WebsiteMaintenanceTasksSeeder extends Seeder
{
    public function run(): void
    {
        $permission = Permission::orderByDesc('id')->first();
        $user = User::where('rank', $permission->id)->first();

        if ($user === null) {
            $user = User::create([
                'username' => 'Admin',
                'password' => Hash::make(Str::password()),
                'account_created' => now()->timestamp,
                'ip_register' => '127.0.0.1',
                'ip_current' => '127.0.0.1',
                'rank' => $permission->id,
            ]);
        }

        WebsiteMaintenanceTask::firstOrCreate(['task' => 'Working on the hotel'], [
            'user_id' => $user->id,
            'completed' => false,
        ]);
    }
}