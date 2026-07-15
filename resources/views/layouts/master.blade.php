<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>STEAM // CLIENT CORE</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;700;800;900&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+39&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/customer-dashboard.css') }}?v={{ time() }}">
    @yield('styles')
</head>
<body>
    <nav class="steam-nav">
        <div class="nav-brand">
            <span class="logo-accent">STEAM</span> // CLIENT CORE
        </div>
        <div class="nav-links">
            <a href="{{ route('customer.dashboard') }}" class="nav-item {{ Route::is('customer.dashboard') ? 'active' : '' }}">HOME</a>
            <a href="{{ route('customer.catalog') }}" class="nav-item {{ Route::is('customer.catalog') ? 'active' : '' }}">CATALOG</a>
            <a href="{{ route('profile') }}" class="nav-item {{ Route::is('profile') ? 'active' : '' }}">MY PROFILE</a>
            <a href="{{ route('my.orders') }}" class="nav-item {{ Route::is('my.orders') ? 'active' : '' }}">MY ORDERS</a>
        </div>
        <div class="user-status-container" style="display: flex; align-items: center; gap: 20px;">
            
            <div class="cart-trigger-container" onclick="toggleWishlistDrawer()" style="position: relative; border: 1px solid #00f0ff; padding: 0 15px; border-radius: 4px; display: flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer; height: 38px; min-width: 90px; color: #00f0ff; font-family: 'Orbitron', sans-serif; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">
                <span>🤍</span>
                <span class="cart-badge-indicator" style="background: #ff003c; position: absolute; top: -8px; right: -8px; padding: 2px 6px; border-radius: 10px; font-size: 0.7rem; color: white;" id="wishlistGlobalCount">0</span>
            </div>
            
            <div class="cart-trigger-container" onclick="toggleCartDrawer()" style="position: relative; border: 1px solid #00f0ff; padding: 0 15px; border-radius: 4px; display: flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer; height: 38px; min-width: 135px; color: #00f0ff; font-family: 'Orbitron', sans-serif; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">
                <span>MY BASKET</span>
                <span class="cart-badge-indicator" style="background: #ff003c; position: absolute; top: -8px; right: -8px; padding: 2px 6px; border-radius: 10px; font-size: 0.7rem; color: white;" id="cartGlobalCount">0</span>
            </div>

            <a href="{{ route('profile') }}"><div class="avatar-glow"></div></a>
            <div class="user-profile-meta">
                <span class="user-display-name" id="userDisplayName" style="font-family: 'Orbitron', sans-serif; font-weight: 700;">{{ Session::get('user_name', 'Operator') }}</span>
                <span class="wallet-balance" style="font-size: 0.8rem; color: #8090a6;">CREDITS: <span class="neon-cyan-text" id="walletBalance" style="color: #00f0ff; font-weight: 600;">${{ number_format($userCredits ?? 250, 2) }}</span></span>
            </div>
            <a href="{{ route('logout') }}" class="btn-logout-cyber" style="border: 1px solid #ff003c; color: #ff003c; padding: 8px 15px; font-family: 'Orbitron', sans-serif; text-decoration: none; border-radius: 4px; font-size: 0.8rem; text-transform: uppercase;">LOGOUT</a>
        </div>
    </nav>

    @yield('content')

    @yield('scripts')
</body>
</html>