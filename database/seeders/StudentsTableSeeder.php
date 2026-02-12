<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StudentsTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 1; $i <= 50; $i++) { // change 50 to 20 if you want fewer
            DB::table('students')->insert([
                'name' => $faker->name,
                'lrn' => $faker->unique()->numberBetween(1000000000, 9999999999),
                'rfid' => 'RFID' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'grade' => $faker->numberBetween(7, 12),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
