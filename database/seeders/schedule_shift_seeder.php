<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class schedule_shift_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('schedule_shift')->insert([
            [
                'shift_id' => 1,
                'schedule_id' => 1,
                'user_id' => 1,
                'initial_shift' => 'holiday',
            ],
            [
                'shift_id' => 2,
                'schedule_id' => 2,
                'user_id' => 2,
                'initial_shift' => 'holiday',
            ],

        ]);
    }
}
