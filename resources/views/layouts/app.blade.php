<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Kasir UMKM')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --ink:#172033; --muted:#64748b; --brand:#6366f1; --brand-dark:#4f46e5; --surface:#fff; --line:#e7eaf3; }
        * { box-sizing:border-box; }
        body { background:#f6f7fb; color:var(--ink); font-family:Inter,sans-serif; }
        .topbar { background:rgba(255,255,255,.92); border-bottom:1px solid var(--line); backdrop-filter:blur(16px); }
        .brand-mark { width:42px;height:42px;display:grid;place-items:center;background:linear-gradient(135deg,#818cf8,#4f46e5);border-radius:13px;color:#fff;box-shadow:0 8px 18px rgba(99,102,241,.3); }
        .brand-name { letter-spacing:-.03em; }
        .card { border:1px solid rgba(226,232,240,.8); border-radius:20px; box-shadow:0 12px 32px rgba(30,41,59,.05); }
        .btn-primary { background:var(--brand); border-color:var(--brand); }
        .btn-primary:hover { background:var(--brand-dark); border-color:var(--brand-dark); }
        .btn-soft { background:#eef2ff;color:var(--brand-dark);border:0; }
        .btn-soft:hover { background:#e0e7ff;color:var(--brand-dark); }
        .alert { border:0;border-radius:14px; }
        .text-brand { color:var(--brand)!important; }
        @media (max-width:575.98px) { .container { padding-left:16px;padding-right:16px; } }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="topbar sticky-top mb-4">
        <div class="container py-3 d-flex align-items-center justify-content-between">
            <a class="text-decoration-none text-dark d-flex align-items-center gap-2" href="{{ route('kasir.index') }}">
                <span class="brand-mark"><i class="fa-solid fa-store"></i></span>
                <span class="brand-name fw-bold fs-5">Kasir<span class="text-brand">UMKM</span><small class="d-block fw-normal text-secondary" style="font-size:.68rem;letter-spacing:0">Point of sale sederhana</small></span>
            </a>
        </div>
    </nav>
    <main class="container pb-5">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert"><i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="fa-solid fa-triangle-exclamation me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @yield('content')
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
