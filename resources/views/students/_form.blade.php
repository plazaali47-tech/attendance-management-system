@php $s = $student ?? null; @endphp
<div class="row g-3">
    <div class="col-md-4">
        <label for="student_no" class="form-label">Student No.</label>
        <input type="text" name="student_no" id="student_no" maxlength="30"
               class="form-control @error('student_no') is-invalid @enderror"
               value="{{ old('student_no', $s->student_no ?? '') }}" placeholder="e.g. 2023-00123">
    </div>
    <div class="col-md-4">
        <label for="last_name" class="form-label">Last Name</label>
        <input type="text" name="last_name" id="last_name" maxlength="100"
               class="form-control @error('last_name') is-invalid @enderror"
               value="{{ old('last_name', $s->last_name ?? '') }}">
    </div>
    <div class="col-md-4">
        <label for="first_name" class="form-label">First Name</label>
        <input type="text" name="first_name" id="first_name" maxlength="100"
               class="form-control @error('first_name') is-invalid @enderror"
               value="{{ old('first_name', $s->first_name ?? '') }}">
    </div>
    <div class="col-md-4">
        <label for="course" class="form-label">Course</label>
        <input type="text" name="course" id="course" maxlength="50"
               class="form-control @error('course') is-invalid @enderror"
               value="{{ old('course', $s->course ?? 'BSIS') }}">
    </div>
    <div class="col-md-4">
        <label for="year_level" class="form-label">Year Level</label>
        <select name="year_level" id="year_level" class="form-select @error('year_level') is-invalid @enderror">
            @for($y = 1; $y <= 5; $y++)
                <option value="{{ $y }}" {{ (int) old('year_level', $s->year_level ?? 4) === $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </div>
    <div class="col-md-4">
        <label for="section" class="form-label">Section</label>
        <input type="text" name="section" id="section" maxlength="20"
               class="form-control @error('section') is-invalid @enderror"
               value="{{ old('section', $s->section ?? '') }}" placeholder="e.g. 4B">
    </div>
</div>
