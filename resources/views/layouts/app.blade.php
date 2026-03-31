<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Helpdesk Surabraja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* ===== Default Light Mode ===== */
        body { background-color: #f8f9fa; }
        .alert { transition: opacity 0.5s ease-in-out; }

        /* Logo navbar */
        .navbar-brand img {
            width: 35px;
            height: 35px;
            object-fit: contain;
            background-color: white;
            border-radius: 6px;
            padding: 2px;
            margin-right: 8px;
            box-shadow: 0 0 3px rgba(0, 0, 0, 0.2);
        }

        /* Responsive logo */
        @media (max-width: 576px) {
            .navbar-brand img {
                width: 28px;
                height: 28px;
            }
        }

        /* ===== DARK MODE ===== */
        body.dark-mode {
            background-color: #1e1e1e !important;
            color: #e5e5e5 !important;
        }

        .dark-mode .navbar {
            background-color: #111 !important;
        }

        .dark-mode .card {
            background-color: #2a2a2a !important;
            color: #ddd !important;
            border: 1px solid #444;
        }

        .dark-mode .btn-light {
            background-color: #444 !important;
            color: #fff !important;
            border-color: #666;
        }

        .dark-mode input,
        .dark-mode textarea,
        .dark-mode select {
            background-color: #333 !important;
            color: #fff !important;
            border-color: #555 !important;
        }

        .dark-mode .alert {
            background-color: #333 !important;
            color: #eee !important;
            border-color: #555 !important;
        }

        .dark-mode .btn-primary {
            background-color: #0d6efd !important;
        }
    </style>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
    <div class="container">
        
        {{-- 🔹 Logo + Nama --}}
        <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ url('/') }}">
            <img src="{{ asset('/img/logosurabraja.png') }}" alt="Logo Surabraja">
            Helpdesk Surabraja
        </a>

        {{-- 🔹 Menu kanan --}}
        <div class="d-flex align-items-center">
            @if(session('is_admin'))
                <a href="{{ route('dashboard') }}" class="btn btn-light btn-sm me-2">📊 Dashboard</a>
                <a href="{{ route('reports.index') }}" class="btn btn-light btn-sm me-2">📋 Daftar Laporan</a>
                <a href="{{ route('admin.logout') }}" class="btn btn-danger btn-sm me-2">🚪 Logout</a>
            @else
                <a href="{{ route('reports.create') }}" class="btn btn-light btn-sm me-2">📝 Form Laporan</a>
                <a href="{{ route('admin.login') }}" class="btn btn-outline-light btn-sm me-2">🔐 Login Admin</a>
            @endif

            {{-- 🌙 Tombol Dark Mode --}}
            <button id="darkModeToggle" class="btn btn-outline-light btn-sm">
                🌙
            </button>
        </div>
    </div>
</nav>

<div class="container">
    {{-- Notifikasi --}}
    @if(session('success') || session('error') || $errors->any())
        @php
            $type = session('success') ? 'success' : (session('error') ? 'danger' : 'warning');
            $message = session('success') ?? session('error') ?? null;
        @endphp

        <div class="alert alert-{{ $type }} alert-dismissible fade show d-flex justify-content-between align-items-center" role="alert" id="main-alert">
            <div>
                @if($message)
                    {{ $message }}
                @elseif($errors->any())
                    ⚠️ Terjadi kesalahan:
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // ===== Auto-hide alert =====
    setTimeout(() => {
        const alert = document.getElementById('main-alert');
        if (alert) {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }
    }, 3000);

    // ===== DARK MODE TOGGLE =====
    const body = document.body;
    const toggle = document.getElementById('darkModeToggle');

    // Load saved mode
    if (localStorage.getItem('dark-mode') === 'enabled') {
        body.classList.add('dark-mode');
        toggle.textContent = "☀️";
    }

    toggle.addEventListener('click', () => {
        body.classList.toggle('dark-mode');

        if (body.classList.contains('dark-mode')) {
            localStorage.setItem('dark-mode', 'enabled');
            toggle.textContent = "☀️";
        } else {
            localStorage.setItem('dark-mode', 'disabled');
            toggle.textContent = "🌙";
        }
    });
</script>

</body>
</html>
