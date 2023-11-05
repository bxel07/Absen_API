<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('job_levels')->insert([
            [
                'name' => 'Contract',
            ],
            [
                'name' => 'Intern',
            ],
            [
                'name' => 'Full-time'
            ]
        ]);
    }
}
