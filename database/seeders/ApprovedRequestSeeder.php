<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApprovedRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      DB::table('approved_requests')->insert([
            [
                'user_id' => 1,
                'shift_request_id' => 1,
                'leave_request_id' => 1,
                'attendance_request_id' => 1,
                'status' => 'approved',
                'reward_flag' => true,
            ],
            [
                'user_id' => 2,
                'shift_request_id' => 2,
                'leave_request_id' => 2,
                'attendance_request_id' => 2,
                'status' => 'approved',
                'reward_flag' => true,
            ]
        ]);
    }
}
