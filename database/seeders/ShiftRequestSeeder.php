<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      DB::table('shift_requests')->insert([
            [
                'user_id' => 1,
                'on_date' => '2022-01-01',
                'old_shift_start' => '00:00:00',
                'old_shift_end' => '00:00:00',
                'new_shift_start' => '00:00:00',
                'new_shift_end' => '00:00:00',
                'reason' => 'reason 1',
            ],
            [
                'user_id' => 2,
                'on_date' => '2022-01-01',
                'old_shift_start' => '00:00:00',
                'old_shift_end' => '00:00:00',
                'new_shift_start' => '00:00:00',
                'new_shift_end' => '00:00:00',
                'reason' => 'reason 2',
            ]
        ]);
    }
}
