<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    /**
     * Tanan nga attendance records, may filter sang petsa, status, section kag search.
     */
    public function index(Request $request)
    {
        $filters = [
            'from'    => $request->query('from'),
            'to'      => $request->query('to'),
            'status'  => $request->query('status'),
            'section' => $request->query('section'),
            'search'  => trim((string) $request->query('search', '')),
        ];

        $records = Attendance::query()
            ->with('student')
            ->join('students', 'students.id', '=', 'attendances.student_id')
            ->select('attendances.*')
            ->when($filters['from'], fn ($q, $v) => $q->whereDate('attendances.date', '>=', $v))
            ->when($filters['to'], fn ($q, $v) => $q->whereDate('attendances.date', '<=', $v))
            ->when($filters['status'], fn ($q, $v) => $q->where('attendances.status', $v))
            ->when($filters['section'], fn ($q, $v) => $q->where('students.section', $v))
            ->when($filters['search'] !== '', function ($q) use ($filters) {
                $s = $filters['search'];
                $q->where(function ($q) use ($s) {
                    $q->where('students.student_no', 'like', "%{$s}%")
                      ->orWhere('students.first_name', 'like', "%{$s}%")
                      ->orWhere('students.last_name', 'like', "%{$s}%");
                });
            })
            ->orderByDesc('attendances.date')
            ->orderBy('students.last_name')
            ->orderBy('students.first_name')
            ->paginate(15)
            ->withQueryString();

        return view('attendances.index', [
            'records'  => $records,
            'filters'  => $filters,
            'statuses' => Attendance::STATUSES,
            'sections' => $this->sections(),
        ]);
    }

    public function create(Request $request)
    {
        return view('attendances.create', [
            'students'  => Student::orderBy('last_name')->orderBy('first_name')->get(),
            'statuses'  => Attendance::STATUSES,
            'studentId' => $request->query('student_id'),
        ]);
    }

    public function store(Request $request)
    {
        Attendance::create($this->validated($request));

        return redirect()->route('attendances.index')
            ->with('success', 'Attendance record added successfully.');
    }

    public function edit(Attendance $attendance)
    {
        return view('attendances.edit', [
            'attendance' => $attendance,
            'students'   => Student::orderBy('last_name')->orderBy('first_name')->get(),
            'statuses'   => Attendance::STATUSES,
        ]);
    }

    public function update(Request $request, Attendance $attendance)
    {
        $attendance->update($this->validated($request, $attendance));

        return redirect()->route('attendances.index')
            ->with('success', 'Attendance record updated successfully.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return redirect()->back()
            ->with('success', 'Attendance record deleted successfully.');
    }

    /**
     * Take Attendance: tanan nga estudyante sang isa ka section sa isa ka petsa.
     */
    public function take(Request $request)
    {
        $date     = $request->query('date', now()->toDateString());
        $sections = $this->sections();
        $section  = $request->query('section', $sections->first());

        $students = Student::when($section, fn ($q) => $q->where('section', $section))
            ->orderBy('last_name')->orderBy('first_name')->get();

        $existing = Attendance::whereDate('date', $date)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        return view('attendances.take', [
            'date'     => $date,
            'section'  => $section,
            'sections' => $sections,
            'students' => $students,
            'existing' => $existing,
            'statuses' => Attendance::STATUSES,
        ]);
    }

    public function saveTake(Request $request)
    {
        $data = $request->validate([
            'date'                 => 'required|date|before_or_equal:today',
            'section'              => 'nullable|string',
            'records'              => 'required|array',
            'records.*.status'     => ['required', Rule::in(array_keys(Attendance::STATUSES))],
            'records.*.time_in'    => 'nullable|date_format:H:i',
            'records.*.remarks'    => 'nullable|max:255',
        ]);

        $ids = Student::whereIn('id', array_keys($data['records']))->pluck('id')->all();

        DB::transaction(function () use ($data, $ids) {
            foreach ($ids as $id) {
                $row = $data['records'][$id];
                $timeIn = in_array($row['status'], ['present', 'late']) ? ($row['time_in'] ?? null) : null;

                Attendance::updateOrCreate(
                    ['student_id' => $id, 'date' => $data['date']],
                    ['status' => $row['status'], 'time_in' => $timeIn, 'remarks' => $row['remarks'] ?? null]
                );
            }
        });

        return redirect()->route('attendance.take', ['date' => $data['date'], 'section' => $data['section']])
            ->with('success', 'Attendance saved for ' . count($ids) . ' student(s).');
    }

    private function validated(Request $request, ?Attendance $attendance = null): array
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'date'       => [
                'required', 'date', 'before_or_equal:today',
                Rule::unique('attendances')
                    ->where(fn ($q) => $q->where('student_id', $request->input('student_id')))
                    ->ignore($attendance),
            ],
            'status'     => ['required', Rule::in(array_keys(Attendance::STATUSES))],
            'time_in'    => 'nullable|date_format:H:i',
            'remarks'    => 'nullable|max:255',
        ], [
            'date.unique' => 'This student already has an attendance record on that date.',
        ]);

        if (!in_array($data['status'], ['present', 'late'])) {
            $data['time_in'] = null;
        }

        return $data;
    }

    private function sections()
    {
        return Student::select('section')->distinct()->orderBy('section')->pluck('section');
    }
}
