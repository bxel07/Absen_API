<?php

namespace Database\Seeders;

use App\Models\Job_Level;
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
        $this->call(RolesSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ProjectsTableSeeder::class);

        $this->call(job_levels ::class);
        $this->call(job_positions::class);
        $this->call(job_departments::class);

        $this->call(companies::class);
        $this->call(branch::class);
        $this->call(employment::class);

        $this->call(schedule_seeder::class);
        $this->call(shift_seeder::class);
        $this->call(schedule_shift_seeder::class);
        $this->call(point_seed::class);
        $this->call(Task_MemberSeeder::class);
    }
}
