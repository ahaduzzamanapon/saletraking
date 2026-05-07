<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SalesTrack Pro — @yield('title', 'Admin Panel')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @stack('head')
</head>
<body>
    <div class="app-shell">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <div class="brand-icon">📍</div>
                <div class="brand-text">
                    <span class="brand-name">SalesTrack</span>
                    <span class="brand-pro">PRO</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section-label">Main</div>
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">⊞</span><span class="nav-label">Dashboard</span>
                </a>
                <a href="{{ route('admin.map') }}" class="nav-item {{ request()->routeIs('admin.map*') ? 'active' : '' }}">
                    <span class="nav-icon">🗺</span><span class="nav-label">Live Map</span>
                    <span class="nav-badge" id="online-count">—</span>
                </a>

                <div class="nav-section-label">Field Data</div>
                <a href="{{ route('admin.salespersons.index') }}" class="nav-item {{ request()->routeIs('admin.salespersons*') ? 'active' : '' }}">
                    <span class="nav-icon">🚶</span><span class="nav-label">Salespersons</span>
                </a>
                <a href="{{ route('admin.visits.index') }}" class="nav-item {{ request()->routeIs('admin.visits*') ? 'active' : '' }}">
                    <span class="nav-icon">🤝</span><span class="nav-label">Visits</span>
                </a>
                <a href="{{ route('admin.feedback.index') }}" class="nav-item {{ request()->routeIs('admin.feedback*') ? 'active' : '' }}">
                    <span class="nav-icon">📋</span><span class="nav-label">Feedback</span>
                </a>
                <a href="{{ route('admin.clients.index') }}" class="nav-item {{ request()->routeIs('admin.clients*') ? 'active' : '' }}">
                    <span class="nav-icon">🏢</span><span class="nav-label">Clients</span>
                </a>

                <div class="nav-section-label">Performance</div>
                <a href="{{ route('admin.targets.index') }}" class="nav-item {{ request()->routeIs('admin.targets*') ? 'active' : '' }}">
                    <span class="nav-icon">🎯</span><span class="nav-label">Targets</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="nav-item {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
                    <span class="nav-icon">📊</span><span class="nav-label">Reports</span>
                </a>

                <div class="nav-section-label">Admin</div>
                <a href="{{ route('admin.territories.index') }}" class="nav-item {{ request()->routeIs('admin.territories*') ? 'active' : '' }}">
                    <span class="nav-icon">🗾</span><span class="nav-label">Territories</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <span class="nav-icon">👥</span><span class="nav-label">Users</span>
                </a>
            </nav>

            <div class="sidebar-user">
                <div class="user-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
                <div class="user-info">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-role">{{ ucfirst(Auth::user()->role) }}</div>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn" title="Logout">⏻</button>
                </form>
            </div>
        </aside>

        <!-- Main content -->
        <div class="main-content">
            <header class="topbar">
                <button class="sidebar-toggle" id="sidebar-toggle">☰</button>
                <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
                <div class="topbar-right">
                    <div class="topbar-time" id="topbar-time"></div>
                    <div class="topbar-badge">
                        <span class="dot dot-green"></span>
                        <span id="rep-count">Loading…</span>
                    </div>
                </div>
            </header>

            <main class="page-body">
                @if(session('success'))
                    <div class="alert alert-success">✓ {{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-error">
                        @foreach($errors->all() as $error)
                            <div>✕ {{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="{{ asset('js/admin.js') }}"></script>
    @stack('scripts')
</body>
</html>
