<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      DB::table('tasks')->insert([
            [
                'user_id' => 1,
                'name' => 'Task 1',
                'project_id' => 1,
                'deadline' => '2022-01-01',
                'description' => 'Task 1 description',
                'status' => 'to-do',
                'comment' => 'Comment 1',
            ],
            [
                'user_id' => 2,
                'name' => 'Task 2',
                'project_id' => 2,
                'deadline' => '2022-01-01',
                'description' => 'Task 2 description',
                'status' => 'in progress',
                'comment' => 'Comment 2',
            ],
            [
                'user_id' => 2,
                'name' => 'Task 3',
                'project_id' => 3,
                'deadline' => '2022-01-01',
                'description' => 'Task 3 description',
                'status' => 'completed',
                'comment' => 'Comment 3',
            ]
        ]);
    }
}
