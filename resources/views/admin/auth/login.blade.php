<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SalesTrack Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <div class="auth-logo-icon">📍</div>
            <div class="auth-logo-text">
                <h1>SalesTrack <span style="color:var(--accent-light)">Pro</span></h1>
                <span>Admin Panel</span>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)<div>✕ {{ $error }}</div>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}" class="auth-form">
            @csrf
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="admin@salestrack.com"
                       value="{{ old('email') }}" required autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <div class="form-group" style="flex-direction:row;align-items:center;gap:8px">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember" style="font-size:13px;color:var(--text-secondary);cursor:pointer">Remember me</label>
            </div>
            <button type="submit" class="btn btn-primary">Sign In →</button>
        </form>

        <p style="text-align:center;margin-top:20px;font-size:12px;color:var(--text-muted)">
            This panel is for managers and admins only.<br>
            Salespersons use the mobile app.
        </p>
    </div>
</div>
</body>
</html>
