@extends('layouts.app')
@section('title', 'Edit Attendance Record')

@section('content')
<h1 class="h3 mb-3">Edit Attendance Record</h1>
<div class="card">
    <div class="card-body">
        <form action="{{ route('attendances.update', $attendance) }}" method="POST">
            @csrf
            @method('PUT')
            @include('attendances._form')
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('attendances.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
</div>
@endsection
