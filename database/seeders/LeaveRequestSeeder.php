<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      DB::table('leave_requests')->insert([
            [
                'user_id' => 1,
                'type' => 'sakit',
                'start_date' => '2022-01-01',
                'start_end' => '2022-01-01',
                'reason' => 'reason 1',
                'delegations' => 'delegations 1',
                'upload_file' => 'upload_file 1',
            ],
            [
                'user_id' => 2,
                'type' => 'cuti',
                'start_date' => '2022-01-01',
                'start_end' => '2022-01-01',
                'reason' => 'reason 2',
                'delegations' => 'delegations 2',
                'upload_file' => 'upload_file 2',
            ]
        ]);
    }
}
