@extends('layouts.app')
@section('title', 'Attendance Records')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h3 mb-0">Attendance Records</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('attendance.take') }}" class="btn btn-success"><i class="bi bi-list-check"></i> Take Attendance</a>
        <a href="{{ route('attendances.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Record</a>
    </div>
</div>

<form method="GET" class="card card-body mb-3">
    <div class="row g-2">
        <div class="col-6 col-md-2">
            <label class="form-label small mb-1">From</label>
            <input type="date" name="from" class="form-control" value="{{ $filters['from'] }}">
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label small mb-1">To</label>
            <input type="date" name="to" class="form-control" value="{{ $filters['to'] }}">
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label small mb-1">Status</label>
            <select name="status" class="form-select">
                <option value="">All</option>
                @foreach($statuses as $key => $label)
                    <option value="{{ $key }}" {{ $filters['status'] === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label small mb-1">Section</label>
            <select name="section" class="form-select">
                <option value="">All</option>
                @foreach($sections as $sec)
                    <option value="{{ $sec }}" {{ $filters['section'] === $sec ? 'selected' : '' }}>{{ $sec }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label small mb-1">Student</label>
            <div class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="No. or name" value="{{ $filters['search'] }}">
                <button class="btn btn-outline-primary">Filter</button>
                <a href="{{ route('attendances.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </div>
    </div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Student</th>
                    <th>Section</th>
                    <th>Status</th>
                    <th>Time In</th>
                    <th>Remarks</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                <tr>
                    <td class="text-nowrap">{{ $record->date->format('M d, Y') }}</td>
                    <td><a href="{{ route('students.show', $record->student) }}">{{ $record->student->full_name }}</a></td>
                    <td>{{ $record->student->section }}</td>
                    <td><span class="badge bg-{{ $record->badge }}">{{ $record->status_label }}</span></td>
                    <td>{{ $record->time_in ? \Carbon\Carbon::parse($record->time_in)->format('h:i A') : '—' }}</td>
                    <td>{{ $record->remarks ?: '—' }}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('attendances.edit', $record) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('attendances.destroy', $record) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this attendance record?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No attendance records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $records->links() }}</div>
@endsection
