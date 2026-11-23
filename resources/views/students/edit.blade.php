@extends('layouts.app')
@section('title', 'Edit Student')

@section('content')
<h1 class="h3 mb-3">Edit Student</h1>
<div class="card">
    <div class="card-body">
        <form action="{{ route('students.update', $student) }}" method="POST">
            @csrf
            @method('PUT')
            @include('students._form')
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('students.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
</div>
@endsection
