<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class job_departments extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('departments')->insert([
            [
                "job_position_id" => 1,
                "job_level_id" => 1,
                "name" => "IT Department"
            ],
            [
                "job_position_id" => 2,
                "job_level_id" => 2,
                "name" => "IT Department"
            ],
            [
                "job_position_id" => 3,
                "job_level_id" => 3,
                "name" => "IT Department"
            ]
        ]);
    }
}
