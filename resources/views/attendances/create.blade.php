@extends('layouts.app')
@section('title', 'Add Attendance Record')

@section('content')
<h1 class="h3 mb-3">Add Attendance Record</h1>
<div class="card">
    <div class="card-body">
        <form action="{{ route('attendances.store') }}" method="POST">
            @csrf
            @include('attendances._form')
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ route('attendances.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
</div>
@endsection
