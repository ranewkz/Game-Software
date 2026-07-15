@extends('layouts.master')

@section('content')
    <div class="dashboard-wrapper">
        <section class="spotlight-slider-container">
            <div class="slider-wrapper" id="heroSlider">
                <div class="slider-slide active" style="background-image: linear-gradient(rgba(0,0,0,0.4), rgba(7,9,14,1)), url('https://images.unsplash.com/photo-1635805737707-575885ab0820?auto=format&fit=crop&w=1200&q=80')">
                    <div class="slide-content">
                        <span class="slide-campaign-tag">// ACTIVE CAMPAIGN: BACK TO SCHOOL DEALS</span>
                        <h2 class="slide-title">GEAR UP FOR THE TERM</h2>
                        <p class="slide-desc">Get your student license package. Elevate your tactical skills with premium digital bundles on cooperative catalogs.</p>
                        <div class="slide-action-row">
                            <span class="campaign-badge">COOP BUNDLE ACTIVE</span>
                            <a href="{{ route('customer.catalog') }}" class="btn-slider-action" style="text-decoration:none;">DISCOVER NOW</a>
                        </div>
                    </div>
                </div>
                <div class="slider-slide" style="background-image: linear-gradient(rgba(0,0,0,0.4), rgba(7,9,14,1)), url('https://images.unsplash.com/photo-1618336753974-aae8e04506aa?auto=format&fit=crop&w=1200&q=80')">
                    <div class="slide-content">
                        <span class="slide-campaign-tag">// WINTER EXCLUSIVE: HOLOGRAPHIC REDUCTION</span>
                        <h2 class="slide-title">CD DISCS PRICE CRASH</h2>
                        <p class="slide-desc">All console physical disk ship orders have received a flat price mitigation coupon. Claim your collector CD units.</p>
                        <div class="slide-action-row">
                            <span class="campaign-badge">FLAT $12 DELIVERY SAVER</span>
                            <button class="btn-slider-action" onclick="document.getElementById('cdVaultSection').scrollIntoView({ behavior: 'smooth' });">CD VAULT RELEASES</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="slider-controls">
                <span class="slider-dot active" onclick="setSliderSlide(0)"></span>
                <span class="slider-dot" onclick="setSliderSlide(1)"></span>
            </div>
        </section>

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
                            <button class="btn-wishlist" onclick="event.preventDefault(); event.stopPropagation(); toggleWishlist({{ $game['id'] }}, this)" data-game-id="{{ $game['id'] }}">馃</button>
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
                            <button class="btn-wishlist" onclick="event.preventDefault(); event.stopPropagation(); toggleWishlist({{ $pGame['id'] }}, this)" data-game-id="{{ $pGame['id'] }}">馃</button>
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
    </div>

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
@endsection

@section('scripts')
    <script>
        window.userConfig = {
            name: "{{ Session::get('user_name') }}",
            primaryAddress: "System Default Address",
            savedAddresses: ["System Default Address"]
        };
        window.gamesDatabase = @json($games);
    </script>
    <script src="{{ asset('js/customer-dashboard.js') }}?v={{ time() }}" defer></script>
@endsection