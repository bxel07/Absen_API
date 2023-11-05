<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      DB::table('points')->insert([
            [
                'user_id' => 1,
                'main_points' => 100,
                'reward_points' => 0,
                'flag_reward_points' => true,
                'reward_point_before_claims' => 0,
            ],
            [
                'user_id' => 2,
                'main_points' => 100,
                'reward_points' => 0,
                'flag_reward_points' => true,
                'reward_point_before_claims' => 0,
            ]
        ]);
    }
}