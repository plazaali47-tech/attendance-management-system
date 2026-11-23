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

        // Nagligad nga 7 ka adlaw (pati subong) para sa chart
        $from = now()->subDays(6)->toDateString();
        $rows = Attendance::whereDate('date', '>=', $from)
            ->selectRaw('date, status, COUNT(*) as total')
            ->groupBy('date', 'status')
            ->get();
        $week = collect(range(6, 0))->map(function ($ago) use ($rows) {
            $d = now()->subDays($ago);
            $day = $rows->filter(fn ($r) => \Carbon\Carbon::parse($r->date)->isSameDay($d));
            $counts = [];
            foreach (array_keys(Attendance::STATUSES) as $s) {
                $counts[$s] = (int) $day->where('status', $s)->sum('total');
            }
            return ['label' => $d->format('D'), 'date' => $d->format('M d'), 'counts' => $counts, 'total' => array_sum($counts)];
        });

        $attendedToday = ($todayCounts['present'] ?? 0) + ($todayCounts['late'] ?? 0);

        // Attendance rate sang nagligad nga 30 ka adlaw
        $month = Attendance::whereDate('date', '>=', now()->subDays(29)->toDateString());
        $monthTotal = (clone $month)->count();
        $monthAttended = (clone $month)->whereIn('status', ['present', 'late'])->count();

        return view('dashboard', [
            'today'         => $today,
            'todayCounts'   => $todayCounts,
            'totalStudents' => $totalStudents,
            'recordedToday' => $recordedToday,
            'notRecorded'   => max(0, $totalStudents - $recordedToday),
            'todayRate'     => $recordedToday > 0 ? round($attendedToday / $recordedToday * 100) : null,
            'monthRate'     => $monthTotal > 0 ? round($monthAttended / $monthTotal * 100, 1) : null,
            'mostAbsent'    => $mostAbsent,
            'recent'        => $recent,
            'week'          => $week,
            'statuses'      => Attendance::STATUSES,
        ]);
    }
}
