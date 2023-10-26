<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$this->call(UserSeeder::class);
        $this->call(RolesSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ProjectsTableSeeder::class);
        $this->call(Task_MemberSeeder::class);
    }
}
