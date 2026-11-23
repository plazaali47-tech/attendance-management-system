<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Sample nga attendance sang nagligad nga 5 ka adlaw nga may klase (Lunes-Biyernes).
     */
    public function run()
    {
        $students = Student::orderBy('id')->get();
        $days = [];
        $d = now()->subDay();
        while (count($days) < 5) {
            if (!$d->isWeekend()) {
                $days[] = $d->toDateString();
            }
            $d = $d->subDay();
        }

        // Pattern para pareho gihapon ang resulta kada seed
        $pattern = ['present', 'present', 'present', 'late', 'present', 'absent', 'present', 'excused', 'present', 'present', 'late'];
        $i = 0;

        foreach ($days as $day) {
            foreach ($students as $student) {
                $status = $pattern[$i++ % count($pattern)];
                Attendance::create([
                    'student_id' => $student->id,
                    'date'       => $day,
                    'status'     => $status,
                    'time_in'    => $status === 'present' ? '07:5' . ($i % 10) : ($status === 'late' ? '08:2' . ($i % 10) : null),
                    'remarks'    => $status === 'excused' ? 'With excuse letter' : null,
                ]);
            }
        }
    }
}
