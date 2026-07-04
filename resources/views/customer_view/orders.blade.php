<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STEAM // CLIENT CORE - My Orders</title>
    
    <!-- Matching Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;700;800;900&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/orders.css') }}">
</head>
<body>

    <!-- NAVIGATION BAR -->
    <nav class="top-nav">
        <div class="nav-brand"><span class="logo-accent">STEAM</span> // CLIENT CORE</div>
        <div class="nav-links">
            <a href="{{ route('customer.dashboard') }}">STOREFRONT</a> 
            <a href="{{ route('profile') }}">MY PROFILE</a>
            <a href="{{ route('my.orders') }}" class="active">MY ORDERS</a>
            <a href="#">CD VAULT</a>
        </div>
        <div class="nav-user-area">
            <button class="btn-basket">🛒 MY BASKET <span class="basket-badge">0</span></button>
            <div class="user-info">
                <div class="user-avatar-small"></div>
                <div class="user-details">
                    <span class="name">{{ Session::get('user_name', 'Operator') }}</span>
                    <span class="credits">CREDITS: $250.00</span>
                </div>
            </div>
            <a href="{{ route('logout') }}" class="btn-logout" style="text-decoration: none;">LOGOUT</a>
        </div>
    </nav>

    <!-- MAIN ORDERS UI -->
    <main class="main-content">
        <div class="orders-wrapper">
            
            <div class="orders-header-row">
                <h2 class="section-header">// LOGISTICS & PURCHASE ARCHIVE</h2>
                
                <!-- JS Filter Tabs -->
                <div class="order-filters">
                    <button class="filter-btn active" data-filter="all">ALL LOGS</button>
                    <button class="filter-btn" data-filter="pending">PENDING</button>
                    <button class="filter-btn" data-filter="shipped">SHIPPED</button>
                    <button class="filter-btn" data-filter="delivered">DELIVERED</button>
                </div>
            </div>
            
            <div class="orders-list">
                @if(isset($orders) && $orders->count() > 0)
                    @foreach($orders as $order)
                        <div class="order-card" data-status="{{ strtolower($order->status ?? 'pending') }}">
                            <div class="order-card-header">
                                <div class="order-id">
                                    <span class="label">ORDER ID:</span> 
                                    <span class="value">#STM-{{ $order->id }}</span>
                                </div>
                                @php
                                    $statusClass = 'status-pending';
                                    $statusText = strtolower($order->status ?? 'pending');
                                    if($statusText == 'shipped') $statusClass = 'status-shipped';
                                    if($statusText == 'delivered') $statusClass = 'status-delivered';
                                @endphp
                                <div class="order-status {{ $statusClass }}">
                                    {{ strtoupper($order->status ?? 'PENDING') }}
                                </div>
                            </div>
                            
                            <div class="order-card-body">
                                <div class="detail-column">
                                    <span class="detail-label">TRANSACTION DATE</span>
                                    <span class="detail-value">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }}</span>
                                </div>
                                <div class="detail-column">
                                    <span class="detail-label">PAYMENT GATEWAY</span>
                                    <span class="detail-value">{{ $order->payment_method ?? 'Kpay / System' }}</span>
                                </div>
                                <div class="detail-column">
                                    <span class="detail-label">CARGO PATHWAY</span>
                                    <span class="detail-value">{{ $order->address ?? 'System Default Coord' }}</span>
                                </div>
                                <div class="detail-column total-column">
                                    <span class="detail-label">TOTAL CHARGED</span>
                                    <span class="detail-value neon-price">${{ number_format($order->total ?? 0, 2) }}</span>
                                </div>
                            </div>
                            
                            <div class="order-card-footer">
                                <button class="btn-action" onclick="alert('Order Receipt generating...')">VIEW RECEIPT</button>
                                @if(strtolower($order->status ?? '') == 'shipped')
                                    <button class="btn-action btn-track">TRACK CARGO</button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- EMPTY STATE -->
                    <div class="empty-state">
                        <div class="empty-icon">📂</div>
                        <h3>// NO ARCHIVED TRANSACTIONS</h3>
                        <p>Your logistics databank is currently empty. Visit the storefront to acquire new licenses.</p>
                        <br>
                        <a href="{{ route('customer.dashboard') }}" class="btn-primary" style="text-decoration:none; padding:15px 30px;">ACCESS STOREFRONT</a>
                    </div>
                @endif
            </div>

        </div>
    </main>

    <script src="{{ asset('js/orders.js') }}"></script>
</body>
</html>