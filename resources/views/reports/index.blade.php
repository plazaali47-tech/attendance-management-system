@extends('layouts.app')
@section('title', 'Reports')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h1 class="h3 mb-0">Attendance Report</h1>
        <div class="text-muted">
            {{ \Carbon\Carbon::parse($from)->format('M d, Y') }} – {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}
            · Section: {{ $section ?: 'All' }}
        </div>
    </div>
    <div class="d-flex gap-2 d-print-none">
        <a href="{{ route('reports.export', request()->query()) }}" class="btn btn-outline-success"><i class="bi bi-file-earmark-spreadsheet"></i> Export CSV</a>
        <button type="button" class="btn btn-outline-primary" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
    </div>
</div>

<form method="GET" class="row g-2 align-items-end mb-3 d-print-none">
    <div class="col-6 col-md-3">
        <label class="form-label small mb-1">From</label>
        <input type="date" name="from" class="form-control" value="{{ $from }}">
    </div>
    <div class="col-6 col-md-3">
        <label class="form-label small mb-1">To</label>
        <input type="date" name="to" class="form-control" value="{{ $to }}">
    </div>
    <div class="col-6 col-md-3">
        <label class="form-label small mb-1">Section</label>
        <select name="section" class="form-select">
            <option value="">All</option>
            @foreach($sections as $sec)
                <option value="{{ $sec }}" {{ $section === $sec ? 'selected' : '' }}>{{ $sec }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-6 col-md-3">
        <button class="btn btn-primary w-100">Generate</button>
    </div>
</form>

<div class="row g-3 mb-3">
    <div class="col-6 col-md">
        <div class="card text-center"><div class="card-body py-2">
            <div class="fs-4 fw-bold">{{ $totals['rate'] !== null ? $totals['rate'] . '%' : '—' }}</div>
            <div class="small text-muted">Overall rate</div>
        </div></div>
    </div>
    @foreach(\App\Models\Attendance::STATUSES as $key => $label)
    <div class="col-6 col-md">
        <div class="card text-center"><div class="card-body py-2">
            <div class="fs-4 fw-bold text-{{ \App\Models\Attendance::BADGES[$key] }}">{{ $totals[$key] }}</div>
            <div class="small text-muted">{{ $label }}</div>
        </div></div>
    </div>
    @endforeach
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Student No.</th>
                    <th>Name</th>
                    <th>Section</th>
                    <th class="text-center">Present</th>
                    <th class="text-center">Late</th>
                    <th class="text-center">Absent</th>
                    <th class="text-center">Excused</th>
                    <th class="text-center">Days</th>
                    <th style="min-width:160px">Attendance Rate</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $r)
                    @php $color = $r->rate === null ? 'secondary' : ($r->rate >= 90 ? 'success' : ($r->rate >= 75 ? 'warning' : 'danger')); @endphp
                    <tr>
                        <td>{{ $r->student_no }}</td>
                        <td><a href="{{ route('students.show', $r) }}">{{ $r->full_name }}</a></td>
                        <td>{{ $r->section }}</td>
                        <td class="text-center">{{ $r->present }}</td>
                        <td class="text-center">{{ $r->late }}</td>
                        <td class="text-center">{{ $r->absent }}</td>
                        <td class="text-center">{{ $r->excused }}</td>
                        <td class="text-center">{{ $r->days }}</td>
                        <td>
                            @if($r->rate === null)
                                <span class="text-muted">No records</span>
                            @else
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-fill" style="height:8px">
                                        <div class="progress-bar bg-{{ $color }}" style="width: {{ $r->rate }}%"></div>
                                    </div>
                                    <span class="small fw-semibold" style="width:3.2em">{{ $r->rate }}%</span>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">No students found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<p class="small text-muted mt-2">Attendance rate = (Present + Late) ÷ days recorded. Green ≥ 90%, yellow ≥ 75%, red below 75%.</p>
@endsection
