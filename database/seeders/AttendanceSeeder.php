<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      DB::table('attendances')->insert([
            [
                'schedule_id' => 1,
                'shift_id' => 1,
                'user_id' => 1,
                'clock_in' => '2022-01-01 00:00:00',
                'clock_out' => '2022-01-01 00:00:00',
                'photo' => 'photo.png',
                'shift_schedule' => '2022-01-01',
                'shift' => 'Shift 1',
                'location' => DB::raw("POINT(123.456, 78.90)"),
                'notes' => 'notes 1',
            ],
            [
                'schedule_id' => 2,
                'shift_id' => 2,
                'user_id' => 2,
                'clock_in' => '2022-01-01 00:00:00',
                'clock_out' => '2022-01-01 00:00:00',
                'photo' => 'photo.png',
                'shift_schedule' => '2022-01-01',
                'shift' => 'Shift 2',
                'location' => DB::raw("POINT(123.456, 78.90)"),
                'notes' => 'notes 2',
            ]
        ]);
    }
}
