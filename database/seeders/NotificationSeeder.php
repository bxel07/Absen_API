<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      DB::table('notifications')->insert([
            [
                'title' => 'Selamat Datang',
                'message' => 'Selamat Datang',
                'user_id' => 1,
                'read_status_for_admin' => false,
                'read_status_for_user' => false,
            ],
            [
                'title' => 'Selamat Datang',
                'message' => 'Selamat Datang',
                'user_id' => 2,
                'read_status_for_admin' => true,
                'read_status_for_user' => true,
            ]
        ]);
    }
}