<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      DB::table('f_a_q_s')->insert([
            [
                'question' => 'How do I create an account?',
                'answer' => 'To create an account, go to the login page and enter your email and password.',
            ],
            [
                'question' => 'How do I edit my profile?',
                'answer' => 'To edit your profile, go to the profile page and click on the edit button.',
            ]
        ]);
    }
}