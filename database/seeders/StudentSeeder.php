<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentSeeder extends Seeder
{
    public function run()
    {
        // Clear existing students first
        DB::table('students')->truncate();

        $firstNames = [
            'Juan', 'Maria', 'Jose', 'Ana', 'Pedro', 'Rosa', 'Carlos', 'Sofia',
            'Miguel', 'Isabel', 'Roberto', 'Carmen', 'Antonio', 'Lucia', 'Francisco',
            'Elena', 'Diego', 'Gabriela', 'Andres', 'Patricia', 'Ricardo', 'Beatriz',
            'Fernando', 'Teresa', 'Manuel', 'Cristina', 'Alberto', 'Mariana', 'Rafael',
            'Valentina', 'Sergio', 'Laura', 'Javier', 'Camila', 'Alejandro', 'Andrea',
            'Daniel', 'Natalia', 'Luis', 'Monica', 'Ernesto', 'Veronica', 'Guillermo',
            'Daniela', 'Marcos', 'Adriana', 'Rodrigo', 'Paula'
        ];

        $lastNames = [
            'Dela Cruz', 'Santos', 'Rizal', 'Garcia', 'Reyes', 'Martinez', 'Fernandez',
            'Mendoza', 'Torres', 'Cruz', 'Lopez', 'Alvarez', 'Ramirez', 'Hernandez',
            'Gomez', 'Rodriguez', 'Sanchez', 'Morales', 'Castillo', 'Flores', 'Vargas',
            'Romero', 'Nunez', 'Medina', 'Ortiz', 'Guerrero', 'Navarro', 'Ruiz',
            'Jimenez', 'Diaz', 'Moreno', 'Munoz', 'Rojas', 'Vega', 'Castro', 'Ibarra',
            'Aguirre', 'Campos', 'Pena', 'Silva', 'Molina', 'Ramos', 'Herrera', 'Lara',
            'Perez', 'Soto', 'Velasquez', 'Zamora'
        ];

        $grades = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];

        $students = [];

        // Add your specific students first
        $students[] = [
            'name' => 'Jemarie Acebes',
            'lrn' => '987654321098',
            'rfid' => NULL,
            'grade' => 'Grade 10',
            'created_at' => '2026-02-05 01:41:22',
            'updated_at' => '2026-02-05 06:13:58',
        ];

        $students[] = [
            'name' => 'Nikkerson Doydora',
            'lrn' => '222222222222',
            'rfid' => '2222222',
            'grade' => 'Grade 10',
            'created_at' => '2026-02-05 02:03:10',
            'updated_at' => '2026-02-05 02:03:10',
        ];

        // Generate 48 more students (total 50)
        $usedLrns = ['987654321098', '222222222222'];
        $usedRfids = ['2222222'];

        for ($i = 1; $i <= 48; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            
            // Generate unique 12-digit LRN
            do {
                $lrn = str_pad(rand(100000000000, 999999999999), 12, '0', STR_PAD_LEFT);
            } while (in_array($lrn, $usedLrns));
            $usedLrns[] = $lrn;
            
            // Generate 10-digit RFID (90% have RFID, 10% don't)
            $rfid = null;
            if (rand(0, 100) > 10) {
                do {
                    $rfid = str_pad(rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);
                } while (in_array($rfid, $usedRfids));
                $usedRfids[] = $rfid;
            }
            
            $grade = $grades[array_rand($grades)];
            
            $createdAt = Carbon::create(2026, 2, 5, rand(8, 15), rand(0, 59), rand(0, 59));

            $students[] = [
                'name' => $firstName . ' ' . $lastName,
                'lrn' => $lrn,
                'rfid' => $rfid,
                'grade' => $grade,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        }

        // Insert all students
        DB::table('students')->insert($students);

        $this->command->info('Students seeded successfully!');
        $this->command->info('Total students created: ' . count($students));
    }
}