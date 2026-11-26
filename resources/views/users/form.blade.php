@extends('layouts.app')
@section('title', $user ? 'Edit User' : 'Add User')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <h1 class="h3 mb-3">{{ $user ? 'Edit User' : 'Add User' }}</h1>
        <form action="{{ $user ? route('users.update', $user) : route('users.store') }}" method="POST" class="card card-body">
            @csrf
            @if($user) @method('PUT') @endif
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label" for="name">Full Name</label>
                    <input type="text" name="name" id="name" maxlength="100" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name ?? '') }}">
                </div>
                <div class="col-12">
                    <label class="form-label" for="email">Email (used to sign in)</label>
                    <input type="email" name="email" id="email" maxlength="150" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="password">{{ $user ? 'New Password (optional)' : 'Password' }}</label>
                    <input type="password" name="password" id="password" autocomplete="new-password" class="form-control @error('password') is-invalid @enderror">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="password_confirmation">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password" class="form-control">
                </div>
                <div class="col-12 small text-muted">
                    At least 8 characters with letters and numbers.@if($user) Leave blank to keep the current password.@endif
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="is_admin" value="1" id="is_admin" class="form-check-input" {{ old('is_admin', $user->is_admin ?? false) ? 'checked' : '' }}>
                        <label for="is_admin" class="form-check-label">Admin (can manage user accounts)</label>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button class="btn btn-primary px-4">Save</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
