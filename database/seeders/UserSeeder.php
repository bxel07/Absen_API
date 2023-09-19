<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       DB::table('users')->insert([
            [
                'fullname' => 'Project Manager',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password'),
                'date_of_birth' => '1980-01-01',
                'gender' => 'laki-laki',
                'contact' => '081234567890',
                'religion' => 'Islam',
                'role_id' => 1,
            ],
            [
                'fullname' => 'Member',
                'email' => 'member@gmail.com',
                'password' => Hash::make('password'),
                'date_of_birth' => '1980-01-01',
                'gender' => 'laki-laki',
                'contact' => '081234567890',
                'religion' => 'Islam',
                'role_id' => 2,
            ]
        ]);
    }
}
