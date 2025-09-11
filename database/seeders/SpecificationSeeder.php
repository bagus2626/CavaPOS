<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class SpecificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('specifications')->insert([
            ['name' => 'Storage'],
            ['name' => 'Users'],
            ['name' => 'Custom Domain'],
            ['name' => 'Email Support'],
            ['name' => 'UI/UX Design'],
            ['name' => 'API Integration'],
            ['name' => 'Platforms'],
        ]);

    }
}
