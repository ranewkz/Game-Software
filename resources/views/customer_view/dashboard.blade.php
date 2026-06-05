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
            <a href="#" class="nav-item" onclick="openProfileModal()">MY PROFILE</a>
            <a href="#" class="nav-item" onclick="openOrdersModal()">MY ORDERS</a>
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
        
        <section class="spotlight-slider-container">
            <div class="slider-wrapper" id="heroSlider">
                
                <div class="slider-slide active" style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(7,9,14,1)), url('https://images.unsplash.com/photo-1635805737707-575885ab0820?auto=format&fit=crop&w=1200&q=80')">
                    <div class="slide-content">
                        <span class="slide-campaign-tag">// ACTIVE CAMPAIGN: BACK TO SCHOOL DEALS</span>
                        <h2 class="slide-title">GEAR UP FOR THE TERM</h2>
                        <p class="slide-desc">Get your student license package. Elevate your learning and tactical skills with premium digital bundles on cooperative and adventure catalogs.</p>
                        <div class="slide-action-row">
                            <span class="campaign-badge">COOP BUNDLE ACTIVE</span>
                            <button class="btn-slider-action" onclick="scrollToCatalogAndFilter('Adventure')">DISCOVER NOW</button>
                        </div>
                    </div>
                </div>

                <div class="slider-slide" style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(7,9,14,1)), url('https://images.unsplash.com/photo-1618336753974-aae8e04506aa?auto=format&fit=crop&w=1200&q=80')">
                    <div class="slide-content">
                        <span class="slide-campaign-tag">// WINTER EXCLUSIVE: HOLOGRAPHIC REDUCTION</span>
                        <h2 class="slide-title">CD DISCS PRICE CRASH</h2>
                        <p class="slide-desc">All console physical disk ship orders have received a flat price mitigation coupon. Claim your collector CD units for PS5 & Xbox with secure local shipment.</p>
                        <div class="slide-action-row">
                            <span class="campaign-badge">FLAT $12 DELIVERY SAVER</span>
                            <button class="btn-slider-action" onclick="scrollToVault()">CD VAULT RELEASES</button>
                        </div>
                    </div>
                </div>

                <div class="slider-slide" style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(7,9,14,1)), url('https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=1200&q=80')">
                    <div class="slide-content">
                        <span class="slide-campaign-tag">// TOURNAMENT INTEGRATION: ACTIVATE BATTLEGROUNDS</span>
                        <h2 class="slide-title">VALORANT OUT NOW ON CONSOLE</h2>
                        <p class="slide-desc">Join the tactical shooter revolution. Optimized completely for next-generation gamepad triggers and high-refresh console monitors. 100% Free-to-Play.</p>
                        <div class="slide-action-row">
                            <span class="campaign-badge">FREE ACQUISITION ACTIVE</span>
                            <button class="btn-slider-action" onclick="scrollToCatalogAndSearch('Valorant')">ACQUIRE TOURNAMENT LICENSE</button>
                        </div>
                    </div>
                </div>

            </div>

            <div class="slider-controls">
                <span class="slider-dot active" onclick="setSliderSlide(0)"></span>
                <span class="slider-dot" onclick="setSliderSlide(1)"></span>
                <span class="slider-dot" onclick="setSliderSlide(2)"></span>
            </div>
        </section>

        <section class="cd-vault-shelf" id="cdVaultSection">
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
                        </div>
                        <div class="vault-details">
                            <h4 class="vault-game-title">{{ $pGame['title'] }}</h4>
                            <div class="vault-action-row">
                                <div class="vault-price-stack">
                                    <span class="disc-price-label">CD DISC VERSION</span>
                                    <span class="disc-price">${{ number_format($pGame['price'] + 12.00, 2) }}</span>
                                </div>
                                <button class="btn-vault-buy" onclick="viewProductDetails({{ $pGame['id'] }}, 'physical')">
                                    VIEW DISC
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="filter-terminal">
            <h2 class="section-subtitle">// STOREFRONT FILTER MODULE</h2>
            <div class="filter-controls-grid">
                
                <div class="control-box search-box">
                    <label for="searchGame">SEARCH ARCHIVE</label>
                    <div class="input-wrapper">
                        <input type="text" id="searchGame" placeholder="TYPE GAME NAME..." onkeyup="processFilters()">
                        <span class="search-laser"></span>
                    </div>
                </div>

                <div class="control-box dropdown-box">
                    <label for="deviceFilter">TARGET SYSTEM DEVICE</label>
                    <select id="deviceFilter" onchange="processFilters()">
                        <option value="ALL">ALL DEVICES & CONSOLES</option>
                        <option value="PC">PC / STEAMLINK</option>
                        <option value="PS5">PLAYSTATION 5 (PS5)</option>
                        <option value="Xbox">XBOX SERIES X / S</option>
                        <option value="Switch">NINTENDO SWITCH</option>
                    </select>
                </div>

                <div class="control-box dropdown-box">
                    <label for="formatFilter">EDITION FORMAT</label>
                    <select id="formatFilter" onchange="processFilters()">
                        <option value="ALL">ALL FORMATS (DIGITAL & CD)</option>
                        <option value="DIGITAL">DIGITAL DOWNLOADS ONLY</option>
                        <option value="PHYSICAL">PHYSICAL CD DISCS ONLY</option>
                    </select>
                </div>

                <div class="control-box dropdown-box">
                    <label for="genreFilter">GENRE INDEX</label>
                    <select id="genreFilter" onchange="processFilters()">
                        <option value="ALL">ALL GENRES</option>
                        <option value="RPG">RPG / ROLE PLAYING</option>
                        <option value="Action">ACTION / COMBAT</option>
                        <option value="Adventure">ADVENTURE</option>
                        <option value="Sports">SPORTS</option>
                        <option value="Strategy">STRATEGY</option>
                        <option value="Rogue-like">ROGUE-LIKE</option>
                    </select>
                </div>

                <div class="control-box slider-box">
                    <div class="slider-labels">
                        <label for="priceRange">MAX PRICE CAP</label>
                        <span class="slider-value-badge" id="priceValue">$85.00</span>
                    </div>
                    <input type="range" id="priceRange" min="0" max="85" value="85" step="5" oninput="updatePriceSlider(this.value); processFilters();">
                    <div class="price-bounds">
                        <span>$0 (FREE)</span>
                        <span>$40</span>
                        <span>$85</span>
                    </div>
                </div>

            </div>
        </section>

        <main class="storefront-feed" id="catalogSection">
            <div class="catalog-header-bar">
                <h3 class="feed-title">AVAILABLE LICENSE DIRECTORY</h3>
                <span class="catalog-count" id="gamesCount">SHOWING ALL GAMES</span>
            </div>

            <div class="games-grid" id="gamesContainer">
                @foreach($games as $game)
                    <div class="game-card" 
                         data-id="{{ $game['id'] }}"
                         data-title="{{ strtolower($game['title']) }}"
                         data-platforms="{{ strtolower($game['platform']) }}"
                         data-genre="{{ strtolower($game['genre']) }}"
                         data-price="{{ $game['price'] }}"
                         data-supports-physical="{{ $game['supports_physical'] ? 'true' : 'false' }}">
                        
                        <div class="card-art-container">
                            <img src="{{ $game['image'] }}" class="card-img-art" alt="{{ $game['title'] }}">
                            <span class="game-thumbnail-label">{{ $game['genre'] }}</span>
                            <span class="card-badge {{ $game['stock'] == 0 ? 'badge-out-of-stock' : '' }}">
                                {{ $game['stock'] == 0 ? 'OUT OF STOCK' : $game['status'] }}
                            </span>
                        </div>

                        <div class="card-details">
                            <div class="card-header-row">
                                <h4 class="game-title">{{ $game['title'] }}</h4>
                                <span class="rating-tag">★ {{ $game['rating'] }}</span>
                            </div>
                            
                            <div class="platform-meta-tags">
                                @foreach(explode(',', $game['platform']) as $plat)
                                    <span class="plat-tag">{{ trim($plat) }}</span>
                                @endforeach
                            </div>

                            <div class="stock-status-display">
                                @if($game['stock'] == 0)
                                    <span class="stock-indicator out-of-stock-txt">● OUT OF WAREHOUSE STOCK</span>
                                @elseif($game['stock'] <= 5)
                                    <span class="stock-indicator low-stock-txt">● SEVERE LOW STOCK ({{ $game['stock'] }} UNITS)</span>
                                @else
                                    <span class="stock-indicator in-stock-txt">● SECURE ALLOCATION ({{ $game['stock'] }} IN STOCK)</span>
                                @endif
                            </div>

                            <div class="card-purchase-row">
                                <div class="price-bracket">
                                    <span class="game-price">
                                        @if($game['price'] == 0)
                                            FREE-TO-PLAY
                                        @else
                                            ${{ number_format($game['price'], 2) }}
                                        @endif
                                    </span>
                                </div>
                                <button class="btn-purchase-action" onclick="viewProductDetails({{ $game['id'] }})">
                                    CHOOSE EDITION
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="no-results-card" id="noResults" style="display: none;">
                <div class="no-results-icon">📡</div>
                <h4>ZERO COMPATIBLE INDEXES FOUND</h4>
                <p>No games inside the grid database match your filtered criteria.</p>
                <button class="btn-clear-filters" onclick="resetFilters()">REBOOT FILTER DIRECTORY</button>
            </div>
        </main>
    </div>

    <div class="cart-drawer-overlay" id="cartDrawerOverlay" onclick="toggleCartDrawer()"></div>
    <div class="cart-drawer" id="cartDrawer">
        <div class="drawer-header">
            <h3 class="drawer-title">// SECURE SHOPPING BASKET</h3>
            <button class="btn-drawer-close" onclick="toggleCartDrawer()">&times;</button>
        </div>
        
        <div class="drawer-items-tray" id="cartItemsTray"></div>

        <div class="drawer-footer">
            <div class="summary-line">
                <span>SUBTOTAL:</span>
                <span id="cartSubtotal">$0.00</span>
            </div>
            <div class="summary-line text-muted-dim">
                <span>CD FREIGHT / PREMIUM FEES:</span>
                <span id="cartFreightFee">$0.00</span>
            </div>
            <div class="summary-line divider-top text-neon-cyan">
                <span>TOTAL ESTIMATED BALANCE:</span>
                <span id="cartTotal">$0.00</span>
            </div>
            <button class="btn-drawer-checkout" onclick="openCheckoutProcess()">PROCEED TO CHECKOUT TERMINAL</button>
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
            <h3 class="modal-title">// SECURE ORDER DISPATCH FORM</h3>
            
            <form id="checkoutForm" onsubmit="submitCheckout(event)">
                <div class="checkout-form-group">
                    <label for="checkoutAddressSelect">DELIVERY SHIFT TARGET CODES</label>
                    <select id="checkoutAddressSelect" class="form-select-cyber" onchange="toggleNewAddressInput(this.value)"></select>
                </div>

                <div class="checkout-form-group" id="newAddressGroup" style="display: none;">
                    <label for="newAddressInput">NEW RECIPIENT CARGO PATHWAY</label>
                    <input type="text" id="newAddressInput" class="form-input-cyber" placeholder="INPUT ALTERNATE SHIPPING COORD ADDRESS...">
                </div>

                <div class="checkout-form-group">
                    <label>SECURE PAYMENT METHOD INTEGRATION</label>
                    <div class="payment-pills-row">
                        <label class="payment-pill">
                            <input type="radio" name="paymentMethod" value="Kpay" checked>
                            <span class="pill-label">KBZPay (Kpay)</span>
                        </label>
                        <label class="payment-pill">
                            <input type="radio" name="paymentMethod" value="Wave Money">
                            <span class="pill-label">WavePay</span>
                        </label>
                        <label class="payment-pill">
                            <input type="radio" name="paymentMethod" value="Cash on Delivery">
                            <span class="pill-label">Cash On Delivery</span>
                        </label>
                    </div>
                </div>

                <div class="invoice-estimation-preview" id="checkoutCostBreakdown"></div>
                <button type="submit" class="btn-submit-checkout">AUTHORIZE SYSTEM TRANSACTION</button>
            </form>
        </div>
    </div>

    <div class="cyber-modal-overlay" id="invoiceModal" style="display: none;">
        <div class="cyber-modal-box invoice-modal">
            <button class="modal-close-cross" onclick="closeInvoiceModal()">&times;</button>
            <div class="invoice-header">
                <span class="invoice-logo">STEAM // INVOICE</span>
                <span class="invoice-id-badge" id="invoiceId">// ORDER ID: STM-00000</span>
            </div>
            <h3 class="invoice-headline">TRANSACTION COMPLETION RECEIPT</h3>
            <p class="invoice-meta-desc">Your order has been compiled and linked to your customer profile.</p>

            <div class="invoice-details-card">
                <div class="invoice-row">
                    <span>AGENT NAME:</span>
                    <strong id="invoiceAgentName">PLAYER ONE</strong>
                </div>
                <div class="invoice-row">
                    <span>CARGO PATHWAY COORDS:</span>
                    <strong id="invoiceCargoPathway"></strong>
                </div>
                <div class="invoice-row">
                    <span>GATEWAY SECURE CODE:</span>
                    <strong id="invoicePaymentType"></strong>
                </div>
            </div>

            <div class="invoice-items-list" id="invoiceItemsList"></div>

            <div class="invoice-total-card">
                <div class="invoice-total-row">
                    <span>TOTAL CREDITS CHARGED:</span>
                    <span id="invoiceFinalAmount" class="neon-pink-text">$0.00</span>
                </div>
            </div>
            <button class="btn-modal-close" onclick="closeInvoiceModal()">CONFIRM INTEGRATION</button>
        </div>
    </div>

    <div class="cyber-modal-overlay" id="ordersModal" style="display: none;">
        <div class="cyber-modal-box large-modal">
            <button class="modal-close-cross" onclick="closeOrdersModal()">&times;</button>
            <h3 class="modal-title">// SECURE PURCHASE ROUTING STATS</h3>
            <div class="orders-history-list" id="ordersHistoryContainer"></div>
        </div>
    </div>

    <div class="cyber-modal-overlay" id="profileModal" style="display: none;">
        <div class="cyber-modal-box form-modal">
            <button class="modal-close-cross" onclick="closeProfileModal()">&times;</button>
            <h3 class="modal-title">// PROFILE MANAGEMENT CONSOLE</h3>
            
            <form id="profileEditForm" onsubmit="saveProfileEdits(event)">
                <div class="checkout-form-group">
                    <label for="profileName">AGENT ID NAME</label>
                    <input type="text" id="profileName" class="form-input-cyber" value="" required>
                </div>
                <div class="checkout-form-group">
                    <label for="profileEmail">SECURE EMAIL STRING</label>
                    <input type="email" id="profileEmail" class="form-input-cyber" value="" required>
                </div>
                <div class="checkout-form-group">
                    <label for="profilePhone">OPERATIONAL CONTACT ROUTING STRING</label>
                    <input type="text" id="profilePhone" class="form-input-cyber" value="" required>
                </div>
                <div class="checkout-form-group">
                    <label>MANAGE SHIPPING CARGO PATHS</label>
                    <div id="addressManagerList"></div>
                    <button type="button" class="btn-add-coord" onclick="addNewProfileAddress()">+ ADD ALTERNATE DELIVERY COORDS</button>
                </div>
                <button type="submit" class="btn-submit-checkout">SAVE AGENT CONFIGURATION</button>
            </form>
        </div>
    </div>

    <script>
        window.userConfig = {
            name: "{{ Session::get('user_name') }}",
            email: "{{ Session::get('user_email') }}",
            phone: "{{ Session::get('user_phone') }}",
            primaryAddress: "{{ Session::get('user_address') }}",
            savedAddresses: [
                "{{ Session::get('user_address') }}",
                "Cargo Dock Station B, Insein Township, Yangon"
            ]
        };
        window.gamesDatabase = @json($games);
    </script>
    <script src="{{ asset('js/customer-dashboard.js') }}" defer></script>
</body>
</html>