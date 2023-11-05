<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      DB::table('announcements')->insert([
            [
                'picture' => 'default.png',
                'message' => 'Selamat Datang',
            ],
            [
                'picture' => 'default.png',
                'message' => 'Selamat Datang',
            ]
        ]);
    }
}