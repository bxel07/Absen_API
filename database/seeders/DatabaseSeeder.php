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
        $this->call(ShiftSeeder::class);
        $this->call(ScheduleSeeder::class);
        $this->call(ScheduleShiftSeeder::class);
        $this->call(ShiftRequestSeeder::class);
        $this->call(AttendanceSeeder::class);
        $this->call(AttendanceRequestSeeder::class);
        $this->call(LeaveRequestSeeder::class);
        $this->call(ApprovedRequestSeeder::class);
        $this->call(ProjectsTableSeeder::class);
        $this->call(TaskSeeder::class);
        $this->call(ApprovedTaskSeeder::class);
        $this->call(PointSeeder::class);
        
        $this->call(CompanySeeder::class);
        $this->call(BranchSeeder::class);
        $this->call(JobLevelSeeder::class);
        $this->call(JobPositionSeeder::class);
        $this->call(JobDepartmentSeeder::class);
        $this->call(DepartementSeeder::class);
        $this->call(EmploymentSeeder::class);
        $this->call(FaqSeeder::class);
        $this->call(NotificationSeeder::class);
        
    }
}
