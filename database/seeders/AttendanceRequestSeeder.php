<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      DB::table('attendance_requests')->insert([
            [
                'user_id' => 1,
                'shift' => 'Shift 1',
                'clock_in' => '2022-01-01 00:00:00',
                'clock_out' => '2022-01-01 00:00:00',
                'description' => 'description 1',
                'upload_file' => 'photo.png',
                'point' => DB::raw("ST_GeomFromText('POINT(10 10)')"),
            ],
            [
                'user_id' => 2,
                'shift' => 'Shift 2',
                'clock_in' => '2022-01-01 00:00:00',
                'clock_out' => '2022-01-01 00:00:00',
                'description' => 'description 2',
                'upload_file' => 'photo.png',
                'point' => DB::raw("ST_GeomFromText('POINT(20 20)')"),
            ]
        ]);
    }
}
