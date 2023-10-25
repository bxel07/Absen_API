<?php

namespace Database\Seeders;

use Carbon\Carbon;
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
           'user_id' => 2,
           'approved_tasks_id' => 1,
           'name' => 'Testing',
           'project_title' => 'testing',
           'deadline' => Carbon::now(),
           'description' => 'hallo testing saja',
           'file' => 'test',
           'status' => 'completed'

           ],
           [
           'user_id' => 3,
           'approved_tasks_id' => 2,
           'name' => 'Testing 2',
           'project_title' => 'testing 2',
           'deadline' => Carbon::now(),
           'description' => 'hallo testing saja 2',
           'file' => 'test',
           'status' => 'completed'
           ],
        ]);
    }
}
