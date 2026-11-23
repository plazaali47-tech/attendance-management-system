<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Sample nga mga estudyante (indi tuod nga tawo).
     */
    public function run()
    {
        $students = [
            ['2023-00101', 'Andrea',   'Bautista',  '4A'],
            ['2023-00102', 'Carlo',    'Dela Cruz', '4A'],
            ['2023-00103', 'Bea',      'Garcia',    '4A'],
            ['2023-00104', 'Miguel',   'Lopez',     '4A'],
            ['2023-00105', 'Kristine', 'Mendoza',   '4A'],
            ['2023-00201', 'Paolo',    'Navarro',   '4B'],
            ['2023-00202', 'Jasmine',  'Ocampo',    '4B'],
            ['2023-00203', 'Rafael',   'Ramos',     '4B'],
            ['2023-00204', 'Nicole',   'Santos',    '4B'],
            ['2023-00205', 'Gabriel',  'Torres',    '4B'],
            ['2023-00206', 'Angela',   'Villanueva','4B'],
            ['2023-00207', 'Joshua',   'Reyes',     '4B'],
        ];

        foreach ($students as [$no, $first, $last, $section]) {
            Student::create([
                'student_no' => $no,
                'first_name' => $first,
                'last_name'  => $last,
                'course'     => 'BSIS',
                'year_level' => 4,
                'section'    => $section,
            ]);
        }
    }
}
