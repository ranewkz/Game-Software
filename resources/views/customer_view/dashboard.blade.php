<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>STEAM // HOME TERMINAL</title>
    <!-- Add the ?v={{ time() }} right after .css -->
    <link rel="stylesheet" href="{{ asset('css/customer-dashboard.css') }}?v={{ time() }}">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;700;800;900&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Upper Navigation Terminal -->
    <nav class="steam-nav">
        <div class="nav-brand">
            <span class="logo-accent">STEAM</span> // CLIENT CORE
        </div>
        <div class="nav-links">
            <a href="{{ route('customer.dashboard') }}" class="nav-item active">HOME</a>
            <a href="{{ route('customer.catalog') }}" class="nav-item">CATALOG</a>
            <a href="{{ route('profile') }}" class="nav-item">MY PROFILE</a>
            <a href="{{ route('my.orders') }}" class="nav-item">MY ORDERS</a>
        </div>
        <div class="user-status-container">
            <div class="cart-trigger-container" onclick="toggleWishlistDrawer()">
                <div class="cart-icon-wrapper" style="border-color: var(--neon-pink); color: var(--neon-pink);">
                    🤍
                    <span class="cart-badge-indicator" style="background: var(--neon-pink);" id="wishlistGlobalCount">0</span>
                </div>
            </div>
            <div class="cart-trigger-container" onclick="toggleCartDrawer()">
                <div class="cart-icon-wrapper">
                    🛒
                    <span class="cart-badge-indicator" id="cartGlobalCount">0</span>
                </div>
                <span class="cart-label">MY BASKET</span>
            </div>
            <a href="{{ route('profile') }}"><div class="avatar-glow"></div></a>
            <div class="user-profile-meta">
                <span class="user-display-name" id="userDisplayName">{{ Session::get('user_name', 'Operator') }}</span>
                <span class="wallet-balance">CREDITS: <span class="neon-cyan-text" id="walletBalance">${{ number_format($userCredits ?? 250, 2) }}</span></span>
            </div>
            <a href="{{ route('logout') }}" class="btn-logout-cyber">LOGOUT</a>
        </div>
    </nav>

    <div class="dashboard-wrapper">
        
        <!-- DYNAMIC SEASONAL HERO BANNER SLIDER -->
        <section class="spotlight-slider-container">
            <div class="slider-wrapper" id="heroSlider">
                @foreach($banners as $index => $banner)
                    <div class="slider-slide {{ $index === 0 ? 'active' : '' }}" style="background-image: linear-gradient(rgba(0,0,0,0.4), rgba(7,9,14,1)), url('{{ asset('assets/images/banners/' . $banner->image) }}')">
                        <div class="slide-content">
                            <span class="slide-campaign-tag">{{ $banner->campaign_tag }}</span>
                            <h2 class="slide-title">{{ $banner->title }}</h2>
                            <p class="slide-desc">{{ $banner->description }}</p>
                            <div class="slide-action-row">
                                @if($banner->badge_text)
                                    <span class="campaign-badge">{{ $banner->badge_text }}</span>
                                @endif
                                <a href="{{ route('customer.catalog') }}" class="btn-slider-action" style="text-decoration:none;">{{ $banner->button_text }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="slider-controls">
                <span class="slider-dot active" onclick="setSliderSlide(0)"></span>
                <span class="slider-dot" onclick="setSliderSlide(1)"></span>
            </div>
        </section>

        <!-- FEATURED ACQUISITIONS (New Section) -->
        <section class="featured-section">
            <div class="catalog-header-bar" style="margin-bottom: 20px;">
                <h3 class="feed-title">// FEATURED ACQUISITIONS</h3>
                <a href="{{ route('customer.catalog') }}" class="view-all-link">VIEW ARCHIVE &rarr;</a>
            </div>
            
            <div class="games-grid">
                @foreach($featuredGames as $game)
                    <div class="game-card">
                        <div class="card-art-container">
                            <img src="{{ $game['image'] }}" class="card-img-art" alt="{{ $game['title'] }}">
                            <span class="game-thumbnail-label">{{ $game['genre'] }}</span>
                            <button class="btn-wishlist" onclick="event.preventDefault(); event.stopPropagation(); toggleWishlist({{ $game['id'] }}, this)" data-game-id="{{ $game['id'] }}">🤍</button>
                        </div>
                        <div class="card-details">
                            <div class="card-header-row">
                                <h4 class="game-title">{{ $game['title'] }}</h4>
                            </div>
                            <div class="card-purchase-row">
                                <span class="game-price">${{ number_format($game['price'], 2) }}</span>
                                <button class="btn-purchase-action" onclick="viewProductDetails({{ $game['id'] }})">VIEW</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- CD Vault Shelf -->
        <section class="cd-vault-shelf" id="cdVaultSection" style="margin-top: 50px;">
            <div class="vault-header">
                <h2 class="vault-title">// PS5 & XBOX PHYSICAL CD VAULT</h2>
                <span class="vault-subtext">HOLOGRAPHIC DISC BOXES WITH SECURE ADDRESS CARGO ROUTING</span>
            </div>

            <div class="disc-carousel-grid">
                @foreach($physicalGames as $pGame)
                    <div class="vault-card">
                        <div class="box-art-depth">
                            <img src="{{ $pGame['image'] }}" class="depth-art" alt="{{ $pGame['title'] }}">
                            <div class="hologram-disc">
                                <div class="disc-inner">
                                    <div class="disc-label">{{ $pGame['genre'] }}</div>
                                    <div class="disc-spindle"></div>
                                </div>
                            </div>
                            <span class="console-box-banner {{ str_contains(strtolower($pGame['platform']), 'ps5') ? 'sony-blue' : 'xbox-green' }}">
                                {{ str_contains(strtolower($pGame['platform']), 'ps5') ? 'PS5 COMPATIBLE' : 'XBOX COMPATIBLE' }}
                            </span>
                            <button class="btn-wishlist" onclick="event.preventDefault(); event.stopPropagation(); toggleWishlist({{ $pGame['id'] }}, this)" data-game-id="{{ $pGame['id'] }}">🤍</button>
                        </div>
                        <div class="vault-details">
                            <h4 class="vault-game-title">{{ $pGame['title'] }}</h4>
                            <div class="vault-action-row">
                                <div class="vault-price-stack">
                                    <span class="disc-price-label">CD DISC VERSION</span>
                                    <span class="disc-price">${{ number_format($pGame['price'] + 12.00, 2) }}</span>
                                </div>
                                <button class="btn-vault-buy" onclick="viewProductDetails({{ $pGame['id'] }}, 'physical')">VIEW DISC</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

    <div class="cart-drawer-overlay" id="cartDrawerOverlay" onclick="toggleCartDrawer(); document.getElementById('wishlistDrawer').classList.remove('active');"></div>
    
    <div class="cart-drawer" id="wishlistDrawer" style="border-left: 2px solid var(--neon-pink);">
        <div class="drawer-header">
            <h3 class="drawer-title" style="color: var(--neon-pink);">// WISHLIST ARCHIVE</h3>
            <button class="btn-drawer-close" onclick="toggleWishlistDrawer()">&times;</button>
        </div>
        <div class="drawer-items-tray" id="wishlistItemsTray"></div>
    </div>

    <div class="cart-drawer" id="cartDrawer">
        <div class="drawer-header">
            <h3 class="drawer-title">// SECURE SHOPPING BASKET</h3>
            <button class="btn-drawer-close" onclick="toggleCartDrawer()">&times;</button>
        </div>
        <div class="drawer-items-tray" id="cartItemsTray"></div>
        <div class="drawer-footer">
            <div class="summary-line"><span>SUBTOTAL:</span><span id="cartSubtotal">$0.00</span></div>
            <div class="summary-line text-muted-dim"><span>FREIGHT FEES:</span><span id="cartFreightFee">$0.00</span></div>
            <div class="summary-line divider-top text-neon-cyan"><span>TOTAL:</span><span id="cartTotal">$0.00</span></div>
            <button class="btn-drawer-checkout" onclick="openCheckoutProcess()">PROCEED TO CHECKOUT</button>
        </div>
    </div>

    <div class="cyber-modal-overlay" id="productModal" style="display: none;">
        <div class="cyber-modal-box large-modal">
            <button class="modal-close-cross" onclick="closeProductModal()">&times;</button>
            <div class="product-details-grid" id="productModalContent"></div>
        </div>
    </div>

    <div class="cyber-modal-overlay" id="checkoutModal" style="display: none;">
        <div class="cyber-modal-box form-modal">
            <button class="modal-close-cross" onclick="closeCheckoutModal()">&times;</button>
            <h3 class="modal-title">// SECURE ORDER DISPATCH</h3>
            <form id="checkoutForm" onsubmit="submitCheckout(event)">
                <div class="checkout-form-group">
                    <label>DELIVERY TARGET CODES</label>
                    <select id="checkoutAddressSelect" class="form-select-cyber" onchange="toggleNewAddressInput(this.value)"></select>
                </div>
                <div class="checkout-form-group" id="newAddressGroup" style="display: none;">
                    <label>NEW RECIPIENT CARGO PATHWAY</label>
                    <input type="text" id="newAddressInput" class="form-input-cyber" placeholder="INPUT ALTERNATE COORD...">
                </div>
                <div class="invoice-estimation-preview" id="checkoutCostBreakdown"></div>
                <button type="submit" class="btn-submit-checkout">AUTHORIZE TRANSACTION</button>
            </form>
        </div>
    </div>

    <div class="cyber-modal-overlay" id="invoiceModal" style="display: none;">
        <div class="cyber-modal-box invoice-modal">
            <h3 class="invoice-headline" style="color:var(--neon-blue);">ORDER SUBMITTED SUCCESSFULLY</h3>
            <p style="color:#fff; margin-bottom: 20px;">Your transaction has been written to the database.</p>
            <a href="{{ route('my.orders') }}" class="btn-submit-checkout" style="text-decoration:none; text-align:center;">VIEW IN MY ORDERS</a>
        </div>
    </div>

    <script>
        window.userConfig = {
            name: "{{ Session::get('user_name') }}",
            primaryAddress: "System Default Address",
            savedAddresses: ["System Default Address"]
        };
        window.gamesDatabase = @json($games);
    </script>
    <!-- Add the ?v={{ time() }} right after .js -->
    <script src="{{ asset('js/customer-dashboard.js') }}?v={{ time() }}" defer></script>
</body>
</html>