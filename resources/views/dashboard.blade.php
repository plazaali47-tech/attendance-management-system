@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
@php
    $colors = ['present' => '#16a34a', 'late' => '#f59e0b', 'absent' => '#ef4444', 'excused' => '#94a3b8'];
    $icons  = ['present' => 'bi-check-circle', 'late' => 'bi-clock-history', 'absent' => 'bi-x-circle', 'excused' => 'bi-envelope-paper'];
    $soft   = ['present' => '#dcfce7', 'late' => '#fef3c7', 'absent' => '#fee2e2', 'excused' => '#e2e8f0'];
    $hour = (int) now()->format('G');
    $greet = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');

    // Donut sang subong nga adlaw (conic-gradient)
    $stops = []; $acc = 0;
    foreach ($statuses as $key => $label) {
        $n = $todayCounts[$key] ?? 0;
        if ($recordedToday > 0 && $n > 0) {
            $start = $acc; $acc += $n / $recordedToday * 100;
            $stops[] = "{$colors[$key]} {$start}% {$acc}%";
        }
    }
    $donut = $stops ? 'conic-gradient(' . implode(', ', $stops) . ')' : 'conic-gradient(#e2e8f0 0 100%)';
@endphp

<div class="hero-banner mb-4">
    <div class="row align-items-center g-3 position-relative" style="z-index:1">
        <div class="col-md-8">
            <div class="opacity-75 small">{{ \Carbon\Carbon::parse($today)->format('l, F d, Y') }}</div>
            <h1 class="h3 mb-1">{{ $greet }}, {{ \Illuminate\Support\Str::words(auth()->user()->name, 1, '') }}!</h1>
            <div class="opacity-75">
                @if($notRecorded > 0)
                    {{ $notRecorded }} of {{ $totalStudents }} students have no attendance yet today.
                @else
                    Everyone's attendance is recorded for today. 🎉
                @endif
            </div>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('attendance.take') }}" class="btn btn-light btn-lg fw-bold text-primary shadow-sm">
                <i class="bi bi-check2-square"></i> Take Attendance
            </a>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl">
        <div class="card stat-card h-100">
            <span class="stat-icon" style="background: var(--brand-soft); color: var(--brand)"><i class="bi bi-people"></i></span>
            <div><div class="stat-value">{{ $totalStudents }}</div><div class="stat-label">Students</div></div>
        </div>
    </div>
    @foreach($statuses as $key => $label)
        <div class="col-6 col-xl">
            <div class="card stat-card h-100">
                <span class="stat-icon" style="background: {{ $soft[$key] }}; color: {{ $colors[$key] }}"><i class="bi {{ $icons[$key] }}"></i></span>
                <div><div class="stat-value">{{ $todayCounts[$key] ?? 0 }}</div><div class="stat-label">{{ $label }} today</div></div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">Today at a glance</div>
            <div class="card-body text-center">
                <div class="mx-auto position-relative" style="width:170px;height:170px;border-radius:50%;background: {{ $donut }}">
                    <div class="position-absolute top-50 start-50 translate-middle bg-white rounded-circle d-flex flex-column justify-content-center" style="width:118px;height:118px">
                        <div class="fs-2 fw-bold lh-1">{{ $todayRate !== null ? $todayRate . '%' : '—' }}</div>
                        <div class="small text-muted">attended</div>
                    </div>
                </div>
                <div class="d-flex flex-wrap justify-content-center gap-3 mt-3 small">
                    @foreach($statuses as $key => $label)
                        <span><i class="bi bi-circle-fill" style="color: {{ $colors[$key] }}"></i> {{ $label }}</span>
                    @endforeach
                </div>
                <div class="small text-muted mt-2">Last 30 days: <b class="text-dark">{{ $monthRate !== null ? $monthRate . '%' : '—' }}</b> attendance rate</div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between">
                <span>Last 7 days</span>
                <a href="{{ route('reports.index') }}" class="small fw-normal">Full report</a>
            </div>
            <div class="card-body">
                @php $max = max(1, $week->max('total')); @endphp
                <div class="d-flex align-items-end justify-content-between gap-2" style="height:180px">
                    @foreach($week as $d)
                        <div class="flex-fill text-center h-100 d-flex flex-column justify-content-end" title="{{ $d['date'] }}: {{ $d['total'] }} record(s)">
                            <div class="small fw-bold mb-1">{{ $d['total'] ?: '' }}</div>
                            <div class="mx-auto d-flex flex-column-reverse overflow-hidden" style="width:60%; max-width:42px; height: {{ max(4, $d['total'] / $max * 140) }}px; border-radius: 8px; background:#eef2f7">
                                @foreach($d['counts'] as $key => $n)
                                    @if($n)<div style="height: {{ $n / max(1, $d['total']) * 100 }}%; background: {{ $colors[$key] }}"></div>@endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-between gap-2 mt-2">
                    @foreach($week as $d)
                        <div class="flex-fill text-center small {{ $loop->last ? 'fw-bold text-primary' : 'text-muted' }}">{{ $loop->last ? 'Today' : $d['label'] }}</div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between">
                <span>Recent Records</span>
                <a href="{{ route('attendances.index') }}" class="small fw-normal">View all</a>
            </div>
            <ul class="list-group list-group-flush">
                @forelse($recent as $r)
                    <li class="list-group-item d-flex align-items-center gap-3 py-3">
                        <span class="avatar-sm">{{ mb_substr($r->student->first_name, 0, 1) }}{{ mb_substr($r->student->last_name, 0, 1) }}</span>
                        <div class="flex-fill">
                            <div class="fw-semibold">{{ $r->student->full_name }}</div>
                            <div class="small text-muted">{{ $r->date->format('M d') }} · {{ $r->student->section }}</div>
                        </div>
                        <span class="badge bg-{{ $r->badge }}">{{ $r->status_label }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted py-4 text-center">No records yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-exclamation-triangle text-danger"></i> Most Absences</div>
            <ul class="list-group list-group-flush">
                @forelse($mostAbsent as $i => $s)
                    <li class="list-group-item d-flex align-items-center gap-3 py-3">
                        <span class="text-muted fw-bold" style="width:1.2rem">{{ $i + 1 }}</span>
                        <a href="{{ route('students.show', $s) }}" class="flex-fill fw-semibold text-decoration-none">{{ $s->full_name }}</a>
                        <span class="badge bg-danger">{{ $s->absences }} absent</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted py-4 text-center">No absences recorded. 👏</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
