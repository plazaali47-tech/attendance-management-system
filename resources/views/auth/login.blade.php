<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login · {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    @include('layouts._theme')
    <style>
        .login-art {
            background: linear-gradient(140deg, var(--brand-dark), var(--brand) 55%, var(--brand-2));
            color: #fff; position: relative; overflow: hidden;
        }
        .login-art .bubble { position: absolute; border-radius: 50%; background: rgba(255, 255, 255, .08); }
        .mini-card { background: rgba(255, 255, 255, .14); border-radius: 14px; padding: .8rem 1rem; backdrop-filter: blur(4px); }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row min-vh-100">
            <div class="col-lg-6 login-art d-none d-lg-flex flex-column justify-content-center p-5">
                <span class="bubble" style="width:320px;height:320px;right:-80px;top:-80px"></span>
                <span class="bubble" style="width:200px;height:200px;left:-60px;bottom:-40px"></span>
                <div class="position-relative" style="max-width:460px">
                    <span class="brand-mark mb-4" style="width:60px;height:60px;font-size:1.8rem;background:rgba(255,255,255,.2);box-shadow:none"><i class="bi bi-calendar2-check"></i></span>
                    <h1 class="display-6 fw-bold mb-3">Attendance Management System</h1>
                    <p class="lead opacity-75 mb-4">Mark a whole section in one click, keep every record tidy, and print reports in seconds.</p>
                    <div class="d-flex gap-3">
                        <div class="mini-card"><i class="bi bi-check2-square fs-4"></i><div class="small mt-1">Take attendance</div></div>
                        <div class="mini-card"><i class="bi bi-people fs-4"></i><div class="small mt-1">Student records</div></div>
                        <div class="mini-card"><i class="bi bi-bar-chart-line fs-4"></i><div class="small mt-1">Reports</div></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 d-flex align-items-center justify-content-center p-4">
                <div style="width:100%; max-width:400px">
                    <div class="d-lg-none text-center mb-4">
                        <span class="brand-mark mb-2" style="width:56px;height:56px;font-size:1.6rem"><i class="bi bi-calendar2-check"></i></span>
                        <h1 class="h4">Attendance Management</h1>
                    </div>
                    <h2 class="h3 mb-1">Welcome back 👋</h2>
                    <p class="text-muted mb-4">Sign in to continue to your dashboard.</p>

                    @if(session('success'))
                        <div class="alert alert-success py-2">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white" style="border-radius:10px 0 0 10px"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" id="email" autofocus
                                       class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="you@school.edu">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white" style="border-radius:10px 0 0 10px"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" id="password"
                                       class="form-control @error('password') is-invalid @enderror" placeholder="••••••••">
                                <button type="button" class="btn btn-outline-secondary" style="border-color:#dbe2ee" onclick="var p=document.getElementById('password');p.type=p.type==='password'?'text':'password';this.firstElementChild.classList.toggle('bi-eye');this.firstElementChild.classList.toggle('bi-eye-slash')" title="Show password"><i class="bi bi-eye"></i></button>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="form-check mb-4">
                            <input type="checkbox" name="remember" id="remember" class="form-check-input">
                            <label for="remember" class="form-check-label">Remember me</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2">Sign in <i class="bi bi-arrow-right"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
