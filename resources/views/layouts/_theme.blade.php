<style>
    /* ===== Attendance Management theme (asul) ===== */
    :root {
        --brand: #2563eb;
        --brand-2: #0ea5e9;
        --brand-dark: #1e40af;
        --brand-soft: #eff6ff;
        --ink: #0f172a;
        --muted: #64748b;
        --line: #e5e9f2;
        --bg: #f4f7fd;
    }
    body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; background: var(--bg); color: var(--ink); }
    h1, h2, h3, .h1, .h2, .h3, .h4, .h5 { font-weight: 800; letter-spacing: -.02em; }
    .text-muted { color: var(--muted) !important; }
    a { color: var(--brand); }

    /* Navbar */
    .app-nav { background: #fff; border-bottom: 1px solid var(--line); box-shadow: 0 1px 12px rgba(15, 23, 42, .04); }
    .app-nav .navbar-brand { font-weight: 800; color: var(--ink); display: flex; align-items: center; gap: .55rem; }
    .brand-mark {
        width: 38px; height: 38px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center;
        background: linear-gradient(135deg, var(--brand), var(--brand-2)); color: #fff; font-size: 1.15rem;
        box-shadow: 0 6px 14px rgba(37, 99, 235, .35);
    }
    .app-nav .nav-link { color: var(--muted); font-weight: 600; border-radius: 10px; padding: .5rem .85rem !important; margin: .1rem; }
    .app-nav .nav-link:hover { color: var(--ink); background: #f1f5f9; }
    .app-nav .nav-link.active { color: var(--brand); background: var(--brand-soft); }
    .app-nav .nav-link i { margin-right: .3rem; }
    .user-chip { background: #f1f5f9; border-radius: 999px; padding: .3rem .8rem .3rem .3rem !important; color: var(--ink) !important; }
    .user-chip .avatar-sm { margin-right: .35rem; }

    /* Cards */
    .card { border: 1px solid var(--line); border-radius: 16px; box-shadow: 0 2px 10px rgba(15, 23, 42, .04); }
    .card-header { background: #fff; border-bottom: 1px solid var(--line); border-radius: 16px 16px 0 0 !important; font-weight: 700; padding: .9rem 1.1rem; }
    .card > .list-group { border-radius: 0 0 16px 16px; overflow: hidden; }
    .card > .table-responsive { border-radius: 16px; overflow-x: auto; }
    /* para ang mga radio (btn-check) indi maglapaw sa gwa sang table sa cellphone */
    .table-responsive { position: relative; }
    .card-body.p-0 { overflow: hidden; border-radius: 16px; }

    /* Tables */
    .table { margin-bottom: 0; }
    .table > :not(caption) > * > * { padding: .8rem 1rem; border-color: #eef1f7; }
    .table thead th, .table .table-light th, .table-light {
        background: #f8fafc !important; color: var(--muted); font-size: .75rem; text-transform: uppercase; letter-spacing: .05em; font-weight: 700;
    }
    .table-hover > tbody > tr:hover > * { background: #f8fbff; }
    .table-bordered > :not(caption) > * > * { border-width: 0 0 1px; }

    /* Buttons */
    .btn { border-radius: 10px; font-weight: 600; }
    .btn-primary { background: linear-gradient(135deg, var(--brand), var(--brand-2)); border: 0; box-shadow: 0 4px 12px rgba(37, 99, 235, .25); }
    .btn-primary:hover, .btn-primary:focus { background: linear-gradient(135deg, var(--brand-dark), var(--brand)); }
    .btn-success { background: #16a34a; border-color: #16a34a; }
    .btn-sm { border-radius: 8px; }
    .btn-info { background: #e0f2fe; border-color: #e0f2fe; color: #0369a1; }
    .btn-info:hover { background: #bae6fd; border-color: #bae6fd; color: #075985; }
    .btn-warning { background: #fef3c7; border-color: #fef3c7; color: #92400e; }
    .btn-warning:hover { background: #fde68a; border-color: #fde68a; color: #78350f; }
    .btn-danger.btn-sm { background: #fee2e2; border-color: #fee2e2; color: #b91c1c; }
    .btn-danger.btn-sm:hover { background: #fecaca; border-color: #fecaca; color: #991b1b; }

    /* Forms */
    .form-control, .form-select { border-radius: 10px; border-color: #dbe2ee; padding: .55rem .8rem; }
    .form-control:focus, .form-select:focus { border-color: var(--brand); box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .12); }
    .form-label { font-weight: 600; font-size: .9rem; }

    /* Soft status pills */
    .badge { font-weight: 700; padding: .4em .75em; border-radius: 999px; }
    .badge.bg-success { background: #dcfce7 !important; color: #15803d; }
    .badge.bg-warning { background: #fef3c7 !important; color: #b45309; }
    .badge.bg-danger { background: #fee2e2 !important; color: #b91c1c; }
    .badge.bg-secondary { background: #e2e8f0 !important; color: #475569; }

    .alert { border: 0; border-radius: 14px; }
    .alert-success { background: #dcfce7; color: #166534; }
    .alert-danger { background: #fee2e2; color: #991b1b; }

    /* Shared pieces */
    .page-head { display: flex; flex-wrap: wrap; align-items: flex-end; gap: .75rem; margin-bottom: 1.25rem; }
    .page-head h1 { margin: 0; }
    .page-head .sub { color: var(--muted); }
    .avatar-sm {
        width: 34px; height: 34px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;
        font-size: .78rem; font-weight: 800; color: #fff; flex-shrink: 0;
        background: linear-gradient(135deg, var(--brand), var(--brand-2));
    }
    .stat-card { padding: 1.1rem; display: flex; align-items: center; gap: .9rem; }
    .stat-icon { width: 48px; height: 48px; border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0; }
    .stat-value { font-size: 1.7rem; font-weight: 800; line-height: 1; }
    .stat-label { color: var(--muted); font-size: .82rem; }

    .hero-banner {
        border-radius: 20px; padding: 1.5rem; color: #fff; position: relative; overflow: hidden;
        background: linear-gradient(120deg, var(--brand-dark), var(--brand) 55%, var(--brand-2));
    }
    .hero-banner::after {
        content: ''; position: absolute; right: -60px; top: -60px; width: 220px; height: 220px; border-radius: 50%;
        background: rgba(255, 255, 255, .1);
    }

    .page-link { border-radius: 8px; margin: 0 2px; color: var(--brand); border-color: var(--line); }
    .page-item.active .page-link { background: var(--brand); border-color: var(--brand); }

    @media print {
        body { background: #fff; }
        .card { box-shadow: none; }
    }
</style>
