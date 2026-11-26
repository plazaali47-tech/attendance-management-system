<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Attendance') · {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    @include('layouts._theme')
</head>
<body>
    @php
        $links = [
            ['dashboard',          'dashboard',      'bi-grid-1x2',        'Dashboard'],
            ['students.index',     'students.*',     'bi-people',          'Students'],
            ['attendance.take',    'attendance.take*', 'bi-check2-square', 'Take Attendance'],
            ['attendances.index',  'attendances.*',  'bi-journal-text',    'Records'],
            ['reports.index',      'reports.*',      'bi-bar-chart-line',  'Reports'],
            ['posts.index',        'posts.*',        'bi-megaphone',       'Posts'],
        ];
        $me = auth()->user();
        $initials = collect(explode(' ', $me->name))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');
    @endphp

    <nav class="navbar navbar-expand-lg navbar-light app-nav sticky-top d-print-none">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <span class="brand-mark"><i class="bi bi-calendar2-check"></i></span>
                <span>Attendance<span class="d-none d-sm-inline"> Management</span></span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-auto">
                    @foreach($links as [$route, $pattern, $icon, $label])
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs($pattern) ? 'active' : '' }}" href="{{ route($route) }}"><i class="bi {{ $icon }}"></i>{{ $label }}</a>
                        </li>
                    @endforeach
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link user-chip dropdown-toggle d-flex align-items-center" href="#" id="userMenu" role="button" data-bs-toggle="dropdown">
                            <span class="avatar-sm">{{ mb_strtoupper($initials) }}</span>{{ $me->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                            <li class="dropdown-item-text small text-muted">{{ $me->email }}@if($me->is_admin) · <b>Admin</b>@endif</li>
                            @if($me->is_admin)
                                <li><a class="dropdown-item" href="{{ route('users.index') }}"><i class="bi bi-people-fill"></i> Manage Users</a></li>
                            @endif
                            <li><a class="dropdown-item" href="{{ route('password.change') }}"><i class="bi bi-shield-lock"></i> Change Password</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right"></i> Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i><div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger d-flex gap-2">
                <i class="bi bi-exclamation-octagon-fill"></i>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="text-center small text-muted pb-4 d-print-none">Attendance Management System · BSIS 4B</footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
