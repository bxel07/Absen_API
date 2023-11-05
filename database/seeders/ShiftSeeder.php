<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('shifts')->insert([
            [
                'name' => 'Shift Pagi',
                'schedule_in' => '2023-10-14 08:00:00',
                'schedule_out' => '2023-10-14 16:00:00',
                'break_start' => '2023-10-14 12:00:00',
                'break_end' => '2023-10-14 13:00:00'
            ],
            [
                'name' => 'Shift Siang',
                'schedule_in' => '2023-10-14 12:00:00',
                'schedule_out' => '2023-10-14 20:00:00',
                'break_start' => '2023-10-14 15:00:00',
                'break_end' => '2023-10-14 16:00:00'
            ],
            [
                'name' => 'Shift Malam',
                'schedule_in' => '2023-10-14 16:00:00',
                'schedule_out' => '2023-10-15 00:00:00',
                'break_start' => '2023-10-14 20:00:00',
                'break_end' => '2023-10-14 21:00:00'
            ],
        ]);
    }
}
