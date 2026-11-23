@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h1 class="h3 mb-0">Dashboard</h1>
        <div class="text-muted">{{ \Carbon\Carbon::parse($today)->format('l, F d, Y') }}</div>
    </div>
    <a href="{{ route('attendance.take') }}" class="btn btn-success"><i class="bi bi-list-check"></i> Take Attendance Today</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-2">
        <div class="card text-center h-100"><div class="card-body">
            <div class="fs-3 fw-bold">{{ $totalStudents }}</div>
            <div class="text-muted small">Students</div>
        </div></div>
    </div>
    @foreach($statuses as $key => $label)
    <div class="col-6 col-lg-2">
        <div class="card text-center h-100"><div class="card-body">
            <div class="fs-3 fw-bold text-{{ \App\Models\Attendance::BADGES[$key] }}">{{ $todayCounts[$key] ?? 0 }}</div>
            <div class="text-muted small">{{ $label }} today</div>
        </div></div>
    </div>
    @endforeach
    <div class="col-6 col-lg-2">
        <div class="card text-center h-100"><div class="card-body">
            <div class="fs-3 fw-bold text-muted">{{ $notRecorded }}</div>
            <div class="text-muted small">Not yet recorded</div>
        </div></div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between">
                <strong>Recent Records</strong>
                <a href="{{ route('attendances.index') }}" class="small">View all</a>
            </div>
            <ul class="list-group list-group-flush">
                @forelse($recent as $r)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            {{ $r->student->full_name }}
                            <span class="text-muted small">· {{ $r->date->format('M d') }}</span>
                        </span>
                        <span class="badge bg-{{ $r->badge }}">{{ $r->status_label }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">No records yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header"><strong>Most Absences</strong></div>
            <ul class="list-group list-group-flush">
                @forelse($mostAbsent as $s)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="{{ route('students.show', $s) }}">{{ $s->full_name }}</a>
                        <span class="badge bg-danger">{{ $s->absences }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">No absences recorded.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
