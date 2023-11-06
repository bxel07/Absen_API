<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ProjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('projects')->insert([
            [
                'task_member_id' => 1,
                'user_id' => 1,
                'name' => 'Project 1',
                'project_title' => 'Title 1',
                'deadline' => '2022-12-31',
                'description' => 'Description for Project 1',
                'reward_point' => 10000,
                'file' => 'project1.pdf',
                'status' => 'to-do',
            ],
            [
                'task_member_id' => 2,
                'user_id' => 2,
                'name' => 'Project 2',
                'project_title' => 'Title 2',
                'deadline' => '2023-01-01',
                'description' => 'Description for Project 2',
                'reward_point' => 10000,
                'file' => 'project2.pdf',
                'status' => 'in progress',
            ],
            // Tambahkan data lainnya sesuai kebutuhan
        ]);
    }
}
