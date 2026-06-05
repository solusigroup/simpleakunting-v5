<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Central Admin') - SimpleAkunting</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: linear-gradient(135deg, #0a1628, #1a2a4a, #0d1b2a); color: #e0e0e0; min-height: 100vh; }
        
        /* Navbar */
        .navbar { background: rgba(0,0,0,0.3); border-bottom: 1px solid rgba(255,255,255,0.06); padding: 0 24px; }
        .navbar-inner { max-width: 900px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; height: 56px; }
        .navbar-brand { font-size: 1.1rem; font-weight: 700; color: #ff8c00; text-decoration: none; }
        .navbar-brand span { color: #fff; }
        .navbar-nav { display: flex; align-items: center; gap: 6px; }
        .navbar-nav a { color: #8fa8c8; text-decoration: none; padding: 8px 14px; border-radius: 6px; font-size: 0.85rem; transition: all 0.2s; }
        .navbar-nav a:hover, .navbar-nav a.active { background: rgba(255,255,255,0.08); color: #fff; }
        .navbar-user { display: flex; align-items: center; gap: 12px; }
        .navbar-user span { color: #8fa8c8; font-size: 0.85rem; }
        .btn-logout { background: rgba(220,53,69,0.15); border: 1px solid rgba(220,53,69,0.3); color: #ff6b6b; padding: 6px 14px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; font-family: inherit; transition: all 0.2s; }
        .btn-logout:hover { background: rgba(220,53,69,0.3); }

        /* Content */
        .container { max-width: 900px; margin: 0 auto; padding: 32px 24px; }
        .container-fluid { max-width: 100%; margin: 0; padding: 0; }
        h1 { color: #ff8c00; font-size: 1.6rem; margin-bottom: 8px; }
        .subtitle { color: #8fa8c8; margin-bottom: 24px; }
        .actions { margin-bottom: 24px; display: flex; gap: 12px; flex-wrap: wrap; }
        .actions a, .actions button { padding: 10px 20px; background: linear-gradient(135deg, #ff8c00, #e67600); color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 0.9rem; border: none; cursor: pointer; font-family: inherit; }
        .actions a.secondary { background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12); }
        
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; }
        .alert-success { background: rgba(40,167,69,0.15); border: 1px solid rgba(40,167,69,0.3); color: #51cf66; }
        .alert-error { background: rgba(220,53,69,0.15); border: 1px solid rgba(220,53,69,0.3); color: #ff6b6b; }
        
        table { width: 100%; border-collapse: collapse; background: rgba(255,255,255,0.04); border-radius: 12px; overflow: hidden; }
        thead th { background: rgba(255,255,255,0.06); padding: 14px 16px; text-align: left; color: #a0b4d0; font-size: 0.85rem; font-weight: 600; border-bottom: 1px solid rgba(255,255,255,0.08); }
        tbody td { padding: 14px 16px; border-bottom: 1px solid rgba(255,255,255,0.04); font-size: 0.9rem; }
        tbody tr:hover { background: rgba(255,255,255,0.03); }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .badge-active { background: rgba(40,167,69,0.2); color: #51cf66; }
        .badge-inactive { background: rgba(220,53,69,0.2); color: #ff6b6b; }
        .badge-superuser { background: rgba(255,140,0,0.2); color: #ff8c00; }
        .domain-link { color: #5b9fff; text-decoration: none; }
        .domain-link:hover { text-decoration: underline; }
        .btn-view, .btn-edit, .btn-delete { display: inline-block; padding: 6px 14px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; font-family: inherit; text-decoration: none; border: 1px solid transparent; transition: all 0.2s; }
        .btn-view { border-color: rgba(91, 159, 255, 0.4); color: #5b9fff; }
        .btn-view:hover { background: rgba(91, 159, 255, 0.15); }
        .btn-edit { border-color: rgba(255, 140, 0, 0.4); color: #ff8c00; }
        .btn-edit:hover { background: rgba(255, 140, 0, 0.15); }
        .btn-delete { background: none; border-color: rgba(220,53,69,0.4); color: #ff6b6b; }
        .btn-delete:hover { background: rgba(220,53,69,0.15); }
        .empty { text-align: center; padding: 48px; color: #5a7090; }

        /* Forms */
        .form-card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 32px; max-width: 500px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; color: #a0b4d0; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; }
        .form-group input { width: 100%; padding: 10px 14px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 8px; color: #e0e0e0; font-size: 0.95rem; font-family: inherit; outline: none; transition: border-color 0.2s; }
        .form-group input:focus { border-color: #ff8c00; }
        .form-group .error { color: #ff6b6b; font-size: 0.8rem; margin-top: 4px; }
        .btn-submit { padding: 12px 28px; background: linear-gradient(135deg, #ff8c00, #e67600); color: #fff; border: none; border-radius: 8px; font-weight: 600; font-size: 0.95rem; cursor: pointer; font-family: inherit; transition: transform 0.2s; }
        .btn-submit:hover { transform: translateY(-1px); }

        @yield('extra-styles')
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-inner">
            <a href="{{ route('central.tenants.index') }}" class="navbar-brand">Simple<span>Akunting</span> Central</a>
            <div class="navbar-nav">
                <a href="{{ route('central.tenants.index') }}" class="{{ request()->routeIs('central.tenants.*') || request()->routeIs('central.register-tenant*') ? 'active' : '' }}">🏢 Tenant</a>
                <a href="{{ route('central.users.index') }}" class="{{ request()->routeIs('central.users.*') || request()->routeIs('central.password.*') ? 'active' : '' }}">👥 Users</a>
                <a href="{{ route('central.workflow') }}" class="{{ request()->routeIs('central.workflow') ? 'active' : '' }}">📊 Workflow</a>
                <a href="{{ route('central.security') }}" class="{{ request()->routeIs('central.security') ? 'active' : '' }}">🛡️ Keamanan</a>
            </div>
            <div class="navbar-user">
                <span>👤 {{ Auth::guard('central')->user()->nama_user }}</span>
                <form method="POST" action="{{ route('central.logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-logout">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="@yield('container-class', 'container')">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>

    @yield('scripts')
</body>
</html>
