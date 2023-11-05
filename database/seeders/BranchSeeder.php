<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('branches')->insert([
            [
                'company_id' => 1,
                'name' => 'Surabaya',
            ],
            [
                'company_id' => 2,
                'name' => 'Surabaya',
            ]
        ]);
    }
}
