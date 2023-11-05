<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('schedules')->insert([
            [
                'schedule_shift' => 'empat hari kerja',
                'effective' => '2023-10-15'
            ],
            [
                'schedule_shift' => 'lima hari kerja',
                'effective' => '2023-10-15'
            ],
            [
                'schedule_shift' => 'tujuh hari kerja',
                'effective' => '2023-10-15'
            ],
        ]);
    }
}
