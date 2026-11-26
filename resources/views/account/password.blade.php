@extends('layouts.app')
@section('title', 'Change Password')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-5">
        <h1 class="h3 mb-3"><i class="bi bi-shield-lock"></i> Change Password</h1>
        <form action="{{ route('password.change.update') }}" method="POST" class="card card-body">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label" for="current_password">Current Password</label>
                <input type="password" name="current_password" id="current_password" autocomplete="current-password" class="form-control @error('current_password') is-invalid @enderror">
            </div>
            <div class="mb-3">
                <label class="form-label" for="password">New Password</label>
                <input type="password" name="password" id="password" autocomplete="new-password" class="form-control @error('password') is-invalid @enderror">
                <div class="form-text">At least 8 characters with letters and numbers.</div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="password_confirmation">Confirm New Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password" class="form-control">
            </div>
            <button class="btn btn-primary">Change Password</button>
        </form>
    </div>
</div>
@endsection
