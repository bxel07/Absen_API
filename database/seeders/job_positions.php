<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class job_positions extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('job_positions')->insert([
            [
                'name' => 'UI/UX Developer',
            ],
            [
                'name' => 'Back-End',
            ],
            [
                'name' => 'Front-End'
            ],
            [
                'name' => 'Mobile'
            ]
        ]);
    }
}
