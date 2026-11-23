@extends('layouts.app')
@section('title', $post->title)

@section('content')
<h1 class="h3">{{ $post->title }}</h1>
<p class="text-muted">
    Post #{{ $post->id }} &middot; Created {{ $post->created_at->format('M d, Y h:i A') }}
    @if($post->updated_at != $post->created_at)
        &middot; Updated {{ $post->updated_at->format('M d, Y h:i A') }}
    @endif
</p>

<div class="card mb-3">
    <div class="card-body" style="white-space: pre-line">{{ $post->content }}</div>
</div>

<a href="{{ route('posts.index') }}" class="btn btn-secondary">Back</a>
<a href="{{ route('posts.edit', $post) }}" class="btn btn-warning">Edit</a>
<form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
</form>
@endsection
