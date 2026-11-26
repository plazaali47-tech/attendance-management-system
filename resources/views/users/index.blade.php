@extends('layouts.app')
@section('title', 'Manage Users')

@section('content')
<div class="d-flex flex-wrap align-items-center gap-2 mb-3">
    <div>
        <h1 class="h3 mb-0">Manage Users</h1>
        <div class="text-muted">Accounts that can sign in to the system</div>
    </div>
    <form method="GET" class="ms-auto d-flex gap-2">
        <input type="text" name="search" class="form-control" placeholder="Name or email" value="{{ $search }}">
        <button class="btn btn-outline-secondary">Search</button>
    </form>
    <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="bi bi-person-plus"></i> Add User</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>User</th><th>Role</th><th>Status</th><th>Created</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    @php $isMe = $u->id === auth()->id(); @endphp
                    <tr class="{{ $u->is_active ? '' : 'text-muted' }}">
                        <td>
                            <div class="fw-semibold">{{ $u->name }} @if($isMe)<span class="badge bg-secondary">You</span>@endif</div>
                            <div class="small text-muted">{{ $u->email }}</div>
                        </td>
                        <td>{!! $u->is_admin ? '<span class="badge bg-warning">Admin</span>' : '<span class="badge bg-secondary">User</span>' !!}</td>
                        <td><span class="badge bg-{{ $u->is_active ? 'success' : 'danger' }}">{{ $u->is_active ? 'Active' : 'Deactivated' }}</span></td>
                        <td class="small">{{ $u->created_at ? $u->created_at->format('M d, Y') : '—' }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('users.edit', $u) }}" class="btn btn-sm btn-warning">Edit</a>
                            @unless($isMe)
                                <form action="{{ route('users.toggle', $u) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-info" data-msg="{{ $u->is_active ? 'Deactivate' : 'Activate' }} the account of {{ $u->name }}?" onclick="return confirm(this.dataset.msg)">{{ $u->is_active ? 'Deactivate' : 'Activate' }}</button>
                                </form>
                                <form action="{{ route('users.destroy', $u) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" data-msg="Delete the account of {{ $u->name }}? This cannot be undone." onclick="return confirm(this.dataset.msg)">Delete</button>
                                </form>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $users->links() }}</div>
<p class="small text-muted mt-2">A deactivated account cannot sign in, and is signed out on its next click. You cannot deactivate or delete your own account, and there is always at least one active admin.</p>
@endsection
