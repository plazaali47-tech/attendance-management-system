@extends('layouts.app')
@section('title', 'Edit Post')

@section('content')
<h1 class="h3 mb-3">Edit Post</h1>
<div class="card">
    <div class="card-body">
        <form action="{{ route('posts.update', $post) }}" method="POST">
            @csrf
            @method('PUT')
            @include('posts._form')
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('posts.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>
@endsection
