<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class companies extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('companies')->insert([
            [
                'name' => 'PT Otak Kanan',
            ],
            [
                'name' => 'Jawa Post',
            ],
            [
                'name' => 'Full-time'
            ]
        ]);
    }
}
