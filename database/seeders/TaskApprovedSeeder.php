<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskApprovedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('approved_tasks')->insert([
            [
                'user_id' => 1,
                'task_id' => 1,
                'status' => 'pending',
            ],
            [
                'user_id' => 1,
                'task_id'=> 2,
                'status' => 'pending',
            ],
            ]);
    }
}
