@extends('layouts.app')
@section('title', $student->full_name)

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h1 class="h3 mb-0">{{ $student->full_name }}</h1>
        <div class="text-muted">{{ $student->student_no }} · {{ $student->course }} {{ $student->year_level }}-{{ $student->section }}</div>
    </div>
    <div>
        <a href="{{ route('students.index') }}" class="btn btn-secondary">Back</a>
        <a href="{{ route('students.edit', $student) }}" class="btn btn-warning">Edit</a>
    </div>
</div>

<div class="row g-3 mb-4">
    @foreach($statuses as $key => $label)
        <div class="col-6 col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="fs-3 fw-bold text-{{ \App\Models\Attendance::BADGES[$key] }}">{{ $counts[$key] ?? 0 }}</div>
                    <div class="text-muted">{{ $label }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<h2 class="h5">Attendance History</h2>
<div class="card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr><th>Date</th><th>Status</th><th>Time In</th><th>Remarks</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                <tr>
                    <td>{{ $record->date->format('M d, Y (D)') }}</td>
                    <td><span class="badge bg-{{ $record->badge }}">{{ $record->status_label }}</span></td>
                    <td>{{ $record->time_in ? \Carbon\Carbon::parse($record->time_in)->format('h:i A') : '—' }}</td>
                    <td>{{ $record->remarks ?: '—' }}</td>
                    <td class="text-end"><a href="{{ route('attendances.edit', $record) }}" class="btn btn-sm btn-warning">Edit</a></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No attendance records yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $records->links() }}</div>
@endsection
