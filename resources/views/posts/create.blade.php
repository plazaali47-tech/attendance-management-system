@extends('layouts.app')
@section('title', 'Create Post')

@section('content')
<h1 class="h3 mb-3">Create New Post</h1>
<div class="card">
    <div class="card-body">
        <form action="{{ route('posts.store') }}" method="POST">
            @csrf
            @include('posts._form')
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="{{ route('posts.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>
@endsection
