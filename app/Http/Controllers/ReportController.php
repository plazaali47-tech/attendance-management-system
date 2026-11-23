<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Attendance summary kada estudyante sa napili nga petsa kag section.
     */
    public function index(Request $request)
    {
        [$from, $to, $section, $rows] = $this->build($request);

        $sections = Student::select('section')->distinct()->orderBy('section')->pluck('section');

        $totals = [
            'present' => $rows->sum('present'),
            'late'    => $rows->sum('late'),
            'absent'  => $rows->sum('absent'),
            'excused' => $rows->sum('excused'),
        ];
        $totals['days'] = array_sum($totals);
        $totals['rate'] = $this->rate($totals['present'] + $totals['late'], $totals['days']);

        return view('reports.index', compact('from', 'to', 'section', 'sections', 'rows', 'totals'));
    }

    /**
     * Pag-download sang report bilang CSV (mabuksan sa Excel).
     */
    public function export(Request $request)
    {
        [$from, $to, $section, $rows] = $this->build($request);

        $filename = 'attendance-report_' . ($section ?: 'all') . "_{$from}_to_{$to}.csv";

        return response()->streamDownload(function () use ($rows, $from, $to, $section) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // para husto ang ñ kag iban pa sa Excel
            fputcsv($out, ['Attendance Report', "{$from} to {$to}", 'Section: ' . ($section ?: 'All')]);
            fputcsv($out, ['Student No.', 'Name', 'Section', 'Present', 'Late', 'Absent', 'Excused', 'Total Days', 'Attendance Rate (%)']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->student_no, $r->full_name, $r->section,
                    $r->present, $r->late, $r->absent, $r->excused, $r->days, $r->rate,
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function build(Request $request): array
    {
        $request->validate([
            'from'    => 'nullable|date',
            'to'      => 'nullable|date|after_or_equal:from',
            'section' => 'nullable|string|max:20',
        ]);

        $from    = $request->query('from', now()->startOfMonth()->toDateString());
        $to      = $request->query('to', now()->toDateString());
        $section = $request->query('section');

        $range = fn ($q) => $q->whereBetween('date', [$from, $to]);

        $rows = Student::query()
            ->when($section, fn ($q) => $q->where('section', $section))
            ->withCount([
                'attendances as present' => fn ($q) => $range($q)->where('status', 'present'),
                'attendances as late'    => fn ($q) => $range($q)->where('status', 'late'),
                'attendances as absent'  => fn ($q) => $range($q)->where('status', 'absent'),
                'attendances as excused' => fn ($q) => $range($q)->where('status', 'excused'),
            ])
            ->orderBy('section')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ->map(function ($s) {
                $s->days = $s->present + $s->late + $s->absent + $s->excused;
                // Ang Present kag Late ginaisip nga nagsulod
                $s->rate = $this->rate($s->present + $s->late, $s->days);
                return $s;
            });

        return [$from, $to, $section, $rows];
    }

    private function rate(int $attended, int $days): ?float
    {
        return $days > 0 ? round($attended / $days * 100, 1) : null;
    }
}
