<?php

namespace Database\Seeders;

use App\Models\Community\Teams\WebsiteTeam;
use Illuminate\Database\Seeder;

class WebsiteTeamSeeder extends Seeder
{
    public function run(): void
    {
        $teams = [
            ['rank_name' => 'DJ'],
            ['rank_name' => 'Wired Expert'],
            ['rank_name' => 'Event planner'],
        ];

        WebsiteTeam::query()->upsert($teams, ['rank_name']);
    }
}
