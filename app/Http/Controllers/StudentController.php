<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * Listahan sang estudyante, may search kag filter sang section.
     */
    public function index(Request $request)
    {
        $search  = trim((string) $request->query('search', ''));
        $section = $request->query('section');

        $students = Student::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('student_no', 'like', "%{$search}%")
                      ->orWhere('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%");
                });
            })
            ->when($section, fn ($q) => $q->where('section', $section))
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(10)
            ->withQueryString();

        $sections = Student::select('section')->distinct()->orderBy('section')->pluck('section');

        return view('students.index', compact('students', 'sections', 'search', 'section'));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        Student::create($this->validated($request));

        return redirect()->route('students.index')
            ->with('success', 'Student added successfully.');
    }

    /**
     * Profile sang estudyante kag ang iya attendance history.
     */
    public function show(Student $student)
    {
        $records = $student->attendances()->orderByDesc('date')->paginate(10);

        $counts = $student->attendances()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('students.show', [
            'student'  => $student,
            'records'  => $records,
            'counts'   => $counts,
            'statuses' => Attendance::STATUSES,
        ]);
    }

    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $student->update($this->validated($request, $student));

        return redirect()->route('students.index')
            ->with('success', 'Student updated successfully.');
    }

    /**
     * Pag-delete sang estudyante; madula man ang iya attendance records.
     */
    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('students.index')
            ->with('success', 'Student deleted successfully.');
    }

    private function validated(Request $request, ?Student $student = null): array
    {
        return $request->validate([
            'student_no' => [
                'required', 'max:30',
                Rule::unique('students', 'student_no')->ignore($student),
            ],
            'first_name' => 'required|max:100',
            'last_name'  => 'required|max:100',
            'course'     => 'required|max:50',
            'year_level' => 'required|integer|between:1,5',
            'section'    => 'required|max:20',
        ]);
    }
}
