@php
    $a = $attendance ?? null;
    $selectedStudent = old('student_id', $a->student_id ?? ($studentId ?? ''));
    $selectedStatus  = old('status', $a->status ?? 'present');
    $timeIn = old('time_in', $a && $a->time_in ? substr($a->time_in, 0, 5) : '');
@endphp
<div class="row g-3">
    <div class="col-md-6">
        <label for="student_id" class="form-label">Student</label>
        <select name="student_id" id="student_id" class="form-select @error('student_id') is-invalid @enderror">
            <option value="">— Select student —</option>
            @foreach($students as $st)
                <option value="{{ $st->id }}" {{ (string) $selectedStudent === (string) $st->id ? 'selected' : '' }}>
                    {{ $st->full_name }} ({{ $st->student_no }}, {{ $st->section }})
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label for="date" class="form-label">Date</label>
        <input type="date" name="date" id="date" max="{{ now()->toDateString() }}"
               class="form-control @error('date') is-invalid @enderror"
               value="{{ old('date', $a ? $a->date->toDateString() : now()->toDateString()) }}">
    </div>
    <div class="col-md-6">
        <label for="status" class="form-label">Status</label>
        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
            @foreach($statuses as $key => $label)
                <option value="{{ $key }}" {{ $selectedStatus === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label for="time_in" class="form-label">Time In <small class="text-muted">(Present / Late only)</small></label>
        <input type="time" name="time_in" id="time_in"
               class="form-control @error('time_in') is-invalid @enderror" value="{{ $timeIn }}">
    </div>
    <div class="col-12">
        <label for="remarks" class="form-label">Remarks</label>
        <input type="text" name="remarks" id="remarks" maxlength="255"
               class="form-control @error('remarks') is-invalid @enderror"
               value="{{ old('remarks', $a->remarks ?? '') }}" placeholder="Optional">
    </div>
</div>
