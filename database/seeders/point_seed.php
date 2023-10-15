<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class point_seed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('points')->insert([
            [
                'user_id' => 1,
                'main_points' => 3000,
                'reward_points' => 200
            ],
            [
                'user_id' => 2,
                'main_points' => 4000,
                'reward_points' => 300
            ],
            [
                'user_id' => 3,
                'main_points' => 5000,
                'reward_points' => 400
            ]
        ]);
    }
}
