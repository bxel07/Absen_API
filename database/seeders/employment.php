<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class employment extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

// Perform your insert operations here.


        DB::table('employments')->insert([
            [
                'user_id' => 1,
                'company_id' => 1,
                'branch_id' => 1,
                'department_id' => 1,
                'join_date' => '2023-10-13',
                'end_date' => '2024-03-13'

            ],
            [
                'user_id' => 2,
                'company_id' => 1,
                'branch_id' => 2,
                'department_id' => 2,
                'join_date' => '2023-10-13',
                'end_date' => '2024-03-13'
            ],
            [
                'user_id' => 3,
                'company_id' => 1,
                'branch_id' => 1,
                'department_id' => 3,
                'join_date' => '2023-10-13',
                'end_date' => '2024-03-13'
            ],
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
