@extends('layouts.app')
@section('title', 'Students')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h3 mb-0">Students</h1>
    <a href="{{ route('students.create') }}" class="btn btn-primary"><i class="bi bi-person-plus"></i> Add Student</a>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-6">
        <input type="text" name="search" class="form-control" placeholder="Search student no. or name" value="{{ $search }}">
    </div>
    <div class="col-md-3">
        <select name="section" class="form-select">
            <option value="">All sections</option>
            @foreach($sections as $sec)
                <option value="{{ $sec }}" {{ $section === $sec ? 'selected' : '' }}>{{ $sec }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 d-flex gap-2">
        <button class="btn btn-outline-primary flex-fill">Filter</button>
        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">Clear</a>
    </div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Student No.</th>
                    <th>Name</th>
                    <th>Course</th>
                    <th>Year &amp; Section</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                <tr>
                    <td class="text-muted">{{ $student->student_no }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar-sm">{{ mb_substr($student->first_name, 0, 1) }}{{ mb_substr($student->last_name, 0, 1) }}</span>
                            <a href="{{ route('students.show', $student) }}" class="fw-semibold text-decoration-none text-reset">{{ $student->full_name }}</a>
                        </div>
                    </td>
                    <td>{{ $student->course }}</td>
                    <td>{{ $student->year_level }} - {{ $student->section }}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-info">View</a>
                        <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('students.destroy', $student) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                    data-msg="Delete {{ $student->full_name }}? All attendance records of this student will also be deleted."
                                    onclick="return confirm(this.dataset.msg)">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No students found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $students->links() }}</div>
@endsection
