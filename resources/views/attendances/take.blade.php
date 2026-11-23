@extends('layouts.app')
@section('title', 'Take Attendance')

@section('content')
<h1 class="h3 mb-3">Take Attendance</h1>

<form method="GET" class="row g-2 align-items-end mb-3">
    <div class="col-6 col-md-3">
        <label class="form-label small mb-1">Date</label>
        <input type="date" name="date" class="form-control" value="{{ $date }}" max="{{ now()->toDateString() }}">
    </div>
    <div class="col-6 col-md-3">
        <label class="form-label small mb-1">Section</label>
        <select name="section" class="form-select">
            @foreach($sections as $sec)
                <option value="{{ $sec }}" {{ $section === $sec ? 'selected' : '' }}>{{ $sec }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <button class="btn btn-outline-primary w-100">Load</button>
    </div>
</form>

@if($students->isEmpty())
    <div class="alert alert-info">No students yet. <a href="{{ route('students.create') }}">Add a student</a> first.</div>
@else
<form action="{{ route('attendance.save') }}" method="POST">
    @csrf
    <input type="hidden" name="date" value="{{ $date }}">
    <input type="hidden" name="section" value="{{ $section }}">

    <div class="d-flex flex-wrap gap-2 mb-2">
        <span class="text-muted me-2 align-self-center">
            {{ \Carbon\Carbon::parse($date)->format('l, M d, Y') }} · {{ $students->count() }} student(s) ·
            {{ $existing->count() }} already recorded
        </span>
        <button type="button" class="btn btn-sm btn-outline-success ms-md-auto" onclick="markAll('present')">Mark all Present</button>
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="markAll('absent')">Mark all Absent</button>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Student</th><th>Status</th><th style="width:140px">Time In</th><th>Remarks</th></tr>
                </thead>
                <tbody>
                    @foreach($students as $st)
                        @php
                            $rec = $existing->get($st->id);
                            $cur = old("records.{$st->id}.status", $rec->status ?? 'present');
                            $time = old("records.{$st->id}.time_in", $rec && $rec->time_in ? substr($rec->time_in, 0, 5) : '');
                        @endphp
                        <tr>
                            <td>
                                {{ $st->full_name }}
                                <div class="small text-muted">{{ $st->student_no }}</div>
                            </td>
                            <td class="text-nowrap">
                                @foreach($statuses as $key => $label)
                                    <input type="radio" class="btn-check status-radio" name="records[{{ $st->id }}][status]"
                                           id="s{{ $st->id }}{{ $key }}" value="{{ $key }}" data-status="{{ $key }}" {{ $cur === $key ? 'checked' : '' }}>
                                    <label class="btn btn-sm btn-outline-{{ \App\Models\Attendance::BADGES[$key] }}" for="s{{ $st->id }}{{ $key }}">{{ $label }}</label>
                                @endforeach
                            </td>
                            <td><input type="time" name="records[{{ $st->id }}][time_in]" class="form-control form-control-sm" value="{{ $time }}"></td>
                            <td><input type="text" name="records[{{ $st->id }}][remarks]" class="form-control form-control-sm" maxlength="255"
                                       value="{{ old("records.{$st->id}.remarks", $rec->remarks ?? '') }}"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Attendance</button>
    </div>
</form>

<script>
    var isToday = @json($date === now()->toDateString());

    function nowHHMM() {
        var d = new Date();
        return String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
    }

    // Present/Late: butangan sang oras subong kon wala pa (kon subong nga adlaw lang).
    // Absent/Excused: tangalon ang Time In.
    function syncTime(radio) {
        var time = radio.closest('tr').querySelector('input[type="time"]');
        if (radio.value === 'present' || radio.value === 'late') {
            if (isToday && !time.value) time.value = nowHHMM();
        } else {
            time.value = '';
        }
    }

    function markAll(status) {
        document.querySelectorAll('.status-radio[data-status="' + status + '"]').forEach(function (r) {
            r.checked = true;
            syncTime(r);
        });
    }

    document.querySelectorAll('.status-radio').forEach(function (r) {
        r.addEventListener('change', function () { syncTime(r); });
    });
</script>
@endif
@endsection
