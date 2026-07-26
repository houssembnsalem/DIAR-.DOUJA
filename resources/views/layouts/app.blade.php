<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DIAR DOUJA') - {{ config('app.name') }}</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #64748b;
            --success: #16a34a;
            --warning: #d97706;
            --danger: #dc2626;
            --sidebar-width: 260px;
            --sidebar-bg: #0f172a;
            --sidebar-text: #94a3b8;
            --sidebar-active: #2563eb;
            --topbar-height: 60px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            margin: 0;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            z-index: 1000;
            overflow-y: auto;
            transition: transform 0.3s ease;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            padding: 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            text-decoration: none;
        }

        .sidebar-brand .brand-icon {
            width: 40px; height: 40px;
            background: var(--primary);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; color: white;
            flex-shrink: 0;
        }

        .sidebar-brand .brand-text {
            margin-left: 12px;
        }

        .sidebar-brand .brand-title {
            color: white;
            font-weight: 700;
            font-size: 15px;
            line-height: 1.2;
        }

        .sidebar-brand .brand-subtitle {
            color: var(--sidebar-text);
            font-size: 11px;
        }

        .sidebar-nav { padding: 12px 0; }

        .sidebar-section-label {
            color: #475569;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 8px 20px 4px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 9px 20px;
            color: var(--sidebar-text);
            text-decoration: none;
            border-radius: 0;
            transition: all 0.15s;
            font-size: 14px;
            gap: 10px;
            position: relative;
        }

        .sidebar-link:hover {
            color: white;
            background: rgba(255,255,255,0.05);
        }

        .sidebar-link.active {
            color: white;
            background: rgba(37, 99, 235, 0.2);
        }

        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3px;
            background: var(--primary);
            border-radius: 0 2px 2px 0;
        }

        .sidebar-link i { font-size: 17px; width: 20px; text-align: center; }

        /* Main layout */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Topbar */
        .topbar {
            height: var(--topbar-height);
            background: white;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            padding: 0 24px;
            gap: 16px;
            position: sticky; top: 0; z-index: 100;
        }

        .topbar-title {
            font-weight: 600;
            font-size: 16px;
            flex: 1;
        }

        .topbar-btn {
            background: none;
            border: none;
            color: #64748b;
            font-size: 20px;
            cursor: pointer;
            padding: 4px;
            border-radius: 6px;
            transition: all 0.15s;
        }
        .topbar-btn:hover { background: #f1f5f9; color: #1e293b; }

        .user-badge {
            display: flex; align-items: center; gap: 8px;
            cursor: pointer;
        }

        .user-avatar {
            width: 34px; height: 34px;
            background: var(--primary);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 600; font-size: 13px;
        }

        /* Page content */
        .page-content {
            padding: 24px;
            flex: 1;
        }

        /* Cards */
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .card-header {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 20px;
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
        }

        /* Stat Cards */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .stat-icon {
            width: 52px; height: 52px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }

        .stat-value { font-size: 24px; font-weight: 700; line-height: 1; }
        .stat-label { font-size: 13px; color: #64748b; margin-top: 4px; }

        /* Badges */
        .badge { font-weight: 500; font-size: 12px; }

        /* Tables */
        .table th {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            padding: 12px 16px;
        }
        .table td { padding: 12px 16px; vertical-align: middle; font-size: 14px; }
        .table tbody tr:hover { background: #f8fafc; }

        /* Buttons */
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }

        /* Alerts */
        .alert { border-radius: 10px; border: none; }

        /* Forms */
        .form-control, .form-select {
            border-color: #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }

        .form-label { font-weight: 500; font-size: 14px; margin-bottom: 6px; }

        /* Status colors */
        .status-available { color: var(--success); }
        .status-unavailable { color: var(--danger); }
        .status-maintenance { color: var(--warning); }

        /* Property card */
        .property-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .property-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }

        .property-card .property-img {
            height: 180px;
            background: #e2e8f0;
            object-fit: cover;
            width: 100%;
        }

        .property-card .property-img-placeholder {
            height: 180px;
            background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
            display: flex; align-items: center; justify-content: center;
            font-size: 40px; color: #94a3b8;
        }

        /* Mobile */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
        }

        /* Print */
        @media print {
            .sidebar, .topbar, .no-print { display: none !important; }
            .main-wrapper { margin-left: 0; }
        }
    </style>

    @stack('styles')
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-house-heart-fill"></i></div>
        <div class="brand-text">
            <div class="brand-title">DIAR DOUJA</div>
            <div class="brand-subtitle">Gestion Immobilière</div>
        </div>
    </a>

    <nav class="sidebar-nav">
        <div class="sidebar-section-label">Principale</div>

        <a href="{{ route('admin.dashboard') }}"
           class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> Tableau de bord
        </a>

        <a href="{{ route('admin.calendar') }}"
           class="sidebar-link {{ request()->routeIs('admin.calendar') ? 'active' : '' }}">
            <i class="bi bi-calendar3"></i> Calendrier
        </a>

        <div class="sidebar-section-label mt-2">Gestion</div>

        <a href="{{ route('admin.properties.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.properties.*') ? 'active' : '' }}">
            <i class="bi bi-building"></i> Logements
        </a>

        <a href="{{ route('admin.reservations.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.reservations.*') ? 'active' : '' }}">
            <i class="bi bi-journal-check"></i> Réservations
        </a>

        <a href="{{ route('admin.clients.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> Clients
        </a>

        @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.services.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
            <i class="bi bi-gear-fill"></i> Services
        </a>
        @endif

        @if(auth()->user()->isAdmin())
        <div class="sidebar-section-label mt-2">Finances & Rapports</div>

        <a href="{{ route('admin.finances.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.finances.*') ? 'active' : '' }}">
            <i class="bi bi-cash-coin"></i> Finances
        </a>

        <a href="{{ route('admin.reports.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line-fill"></i> Rapports & Stats
        </a>

        <div class="sidebar-section-label mt-2">Administration</div>

        <a href="{{ route('admin.users.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="bi bi-shield-person-fill"></i> Utilisateurs
        </a>
        @endif
    </nav>

    <!-- Sidebar footer -->
    <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 16px 20px; border-top: 1px solid rgba(255,255,255,0.05);">
        <div style="display:flex; align-items:center; gap:10px;">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div style="flex:1; min-width:0;">
                <div style="color:white; font-size:13px; font-weight:500; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ auth()->user()->name }}</div>
                <div style="color:#64748b; font-size:11px;">{{ auth()->user()->getRoleLabel() }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="topbar-btn" title="Déconnexion" style="color:#64748b;">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- Main -->
<div class="main-wrapper">
    <!-- Topbar -->
    <header class="topbar">
        <button class="topbar-btn d-md-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
            <i class="bi bi-list"></i>
        </button>

        <div class="topbar-title">@yield('title', 'Tableau de bord')</div>

        <!-- Lang switch -->
        <div class="d-flex gap-2 align-items-center">
            <a href="{{ route('lang.switch', 'fr') }}" class="btn btn-sm {{ app()->getLocale() === 'fr' ? 'btn-primary' : 'btn-outline-secondary' }}">FR</a>
            <a href="{{ route('lang.switch', 'en') }}" class="btn btn-sm {{ app()->getLocale() === 'en' ? 'btn-primary' : 'btn-outline-secondary' }}">EN</a>
        </div>

        <!-- Quick add -->
        <a href="{{ route('admin.reservations.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Nouvelle réservation
        </a>
    </header>

    <!-- Page content -->
    <main class="page-content">
        <!-- Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible d-flex align-items-center mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible d-flex align-items-center mb-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible mb-3">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<!-- FullCalendar -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

<script>
    // Auto-dismiss alerts
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(el => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
            bsAlert.close();
        });
    }, 5000);

    // CSRF token for AJAX
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
</script>

@stack('scripts')
</body>
</html>
