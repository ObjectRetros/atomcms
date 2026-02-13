<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            'mail' => fake()->unique()->email(),
            'password' => Hash::make('password'),
            'account_created' => time(),
            'last_login' => time(),
            'look' => setting('start_look') ?: 'hr-100-61.hd-180-1.ch-210-66.lg-270-110.sh-305-62',
            'credits' => setting('start_credits') ?: 1000,
            'ip_register' => '127.0.0.1',
            'ip_current' => '127.0.0.1',
        ];
    }
}
