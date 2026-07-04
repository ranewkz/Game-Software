<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STEAM // STOREFRONT & CART TERMINAL</title>
    <link rel="stylesheet" href="{{ asset('css/customer-dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;700;800;900&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <nav class="steam-nav">
        <div class="nav-brand">
            <span class="logo-accent">STEAM</span> // CLIENT CORE
        </div>
        <div class="nav-links">
            <a href="#" class="nav-item active" onclick="resetFilters()">STOREFRONT</a>
            <a href="{{ route('profile') }}" class="nav-item">MY PROFILE</a>
            <a href="{{ route('my.orders') }}" class="nav-item">MY ORDERS</a>
            <a href="#" class="nav-item text-neon-pink" onclick="scrollToVault()">CD VAULT</a>
        </div>
        <div class="user-status-container">
            <div class="cart-trigger-container" onclick="toggleCartDrawer()">
                <div class="cart-icon-wrapper">
                    🛒
                    <span class="cart-badge-indicator" id="cartGlobalCount">0</span>
                </div>
                <span class="cart-label">MY BASKET</span>
            </div>
            <div class="avatar-glow" onclick="openProfileModal()"></div>
            <div class="user-profile-meta">
                <span class="user-display-name" id="userDisplayName">{{ Session::get('user_name', 'Player One') }}</span>
                <span class="wallet-balance">CREDITS: <span class="neon-cyan-text">$250.00</span></span>
            </div>
            <a href="{{ route('logout') }}" class="btn-logout-cyber">LOGOUT</a>
        </div>
    </nav>

    <div class="dashboard-wrapper">
        
        <!-- DATABASE-DRIVEN HERO SLIDER -->
        <section class="spotlight-slider-container">
            <div class="slider-wrapper" id="heroSlider">
                @if(isset($banners) && count($banners) > 0)
                    @foreach($banners as $index => $banner)
                    <div class="slider-slide {{ $index === 0 ? 'active' : '' }}" 
                         style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(7,9,14,1)), url('{{ asset('assets/images/banners/' . $banner->image) }}')">
                        <div class="slide-content">
                            <span class="slide-campaign-tag">{{ $banner->campaign_tag }}</span>
                            <h2 class="slide-title">{{ $banner->title }}</h2>
                            <p class="slide-desc">{{ $banner->description }}</p>
                            <div class="slide-action-row">
                                @if($banner->badge_text)
                                    <span class="campaign-badge">{{ $banner->badge_text }}</span>
                                @endif
                                <button class="btn-slider-action">{{ $banner->button_text }}</button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="slider-slide active" style="background-color: #111;">
                        <div class="slide-content">
                            <span class="slide-campaign-tag">// SYSTEM</span>
                            <h2 class="slide-title">NO ACTIVE CAMPAIGNS</h2>
                        </div>
                    </div>
                @endif
            </div>

            <div class="slider-controls">
                @if(isset($banners))
                    @foreach($banners as $index => $banner)
                        <span class="slider-dot {{ $index === 0 ? 'active' : '' }}" onclick="setSliderSlide({{ $index }})"></span>
                    @endforeach
                @endif
            </div>
        </section>

        <!-- CD Vault -->
        <section class="cd-vault-shelf" id="cdVaultSection">
            <div class="vault-header">
                <h2 class="vault-title">// PS5 & XBOX PHYSICAL CD VAULT</h2>
            </div>
            <div class="disc-carousel-grid">
                @foreach($physicalGames as $pGame)
                    <div class="vault-card">
                        <div class="box-art-depth">
                            <img src="{{ asset($pGame['image']) }}" class="depth-art" alt="{{ $pGame['title'] }}">
                        </div>
                        <div class="vault-details">
                            <h4 class="vault-game-title">{{ $pGame['title'] }}</h4>
                            <div class="vault-action-row">
                                <div class="vault-price-stack">
                                    <span class="disc-price-label">CD DISC</span>
                                    <span class="disc-price">${{ number_format($pGame['price'] + 12.00, 2) }}</span>
                                </div>
                                <button class="btn-vault-buy" onclick="viewProductDetails({{ $pGame['id'] }}, 'physical')">VIEW DISC</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Filter Search -->
        <section class="filter-terminal">
            <h2 class="section-subtitle">// STOREFRONT FILTER MODULE</h2>
            <div class="filter-controls-grid">
                <div class="control-box search-box">
                    <label>SEARCH ARCHIVE</label>
                    <input type="text" id="searchGame" placeholder="TYPE TITLE..." onkeyup="processFilters()">
                </div>
            </div>
        </section>

        <!-- Main Catalog Feed -->
        <main class="storefront-feed" id="catalogSection">
            <div class="catalog-header-bar">
                <h3 class="feed-title">AVAILABLE LICENSE DIRECTORY</h3>
            </div>
            <div class="games-grid" id="gamesContainer">
                @foreach($games as $game)
                    <div class="game-card">
                        <div class="card-art-container">
                            <img src="{{ asset($game['image']) }}" class="card-img-art" alt="{{ $game['title'] }}">
                        </div>
                        <div class="card-details">
                            <h4 class="game-title">{{ $game['title'] }}</h4>
                            <div class="card-purchase-row">
                                <span class="game-price">${{ number_format($game['price'], 2) }}</span>
                                <button class="btn-purchase-action" onclick="viewProductDetails({{ $game['id'] }})">CHOOSE EDITION</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </main>
    </div>

    <script>
        window.userConfig = {
            name: "{{ Session::get('user_name') }}",
            email: "{{ Session::get('user_email') }}"
        };
        window.gamesDatabase = @json($games);
    </script>
    <script src="{{ asset('js/customer-dashboard.js') }}" defer></script>
</body>
</html>