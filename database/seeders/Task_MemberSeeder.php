<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Task_MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      DB::table('task_members')->insert([
            [
                'user_id' => 1,
            ],
            [
                'user_id' => 2,
            ],
            [
                'user_id' => 3,
            ]
        ]);
    }
}
