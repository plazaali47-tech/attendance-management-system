<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $todayCounts = Attendance::whereDate('date', $today)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalStudents = Student::count();
        $recordedToday = $todayCounts->sum();

        // Mga estudyante nga pinakamadamo absent
        $mostAbsent = Student::withCount(['attendances as absences' => fn ($q) => $q->where('status', 'absent')])
            ->having('absences', '>', 0)
            ->orderByDesc('absences')
            ->orderBy('last_name')
            ->take(5)
            ->get();

        $recent = Attendance::with('student')
            ->orderByDesc('date')
            ->orderByDesc('updated_at')
            ->take(8)
            ->get();

        return view('dashboard', [
            'today'         => $today,
            'todayCounts'   => $todayCounts,
            'totalStudents' => $totalStudents,
            'notRecorded'   => max(0, $totalStudents - $recordedToday),
            'mostAbsent'    => $mostAbsent,
            'recent'        => $recent,
            'statuses'      => Attendance::STATUSES,
        ]);
    }
}
