@extends('layouts.master')

@section('content')
    <div class="dashboard-wrapper">
        <div class="catalog-header-bar" style="margin-top: 20px;">
            <h3 class="feed-title">// MASTER LICENSE ARCHIVE</h3>
        </div>

        <section class="filter-dashboard">
            <div class="filter-header">
                <span class="filter-title">// STOREFRONT FILTER MODULE</span>
                <button class="btn-reset-filters" onclick="resetFilters()">RESET PARAMETERS</button>
            </div>
            <div class="filter-controls-grid">
                <div class="filter-group col-span-full">
                    <label>SEARCH ARCHIVE</label>
                    <input type="text" id="searchGame" class="filter-input text-search" placeholder="TYPE TITLE..." onkeyup="processFilters()">
                </div>
                <div class="filter-group">
                    <label>DEVICE COMPATIBILITY</label>
                    <select id="deviceFilter" class="filter-input" onchange="processFilters()">
                        <option value="all">ALL PLATFORMS</option>
                        <option value="pc">PC WINDOWS</option>
                        <option value="ps5">PLAYSTATION 5</option>
                        <option value="xbox">XBOX SERIES X</option>
                        <option value="switch">NINTENDO SWITCH</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>LICENSE FORMAT</label>
                    <select id="formatFilter" class="filter-input" onchange="processFilters()">
                        <option value="all">ALL FORMATS</option>
                        <option value="digital">DIGITAL ONLY</option>
                        <option value="physical">PHYSICAL CD AVAILABLE</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>GENRE CLASSIFICATION</label>
                    <select id="genreFilter" class="filter-input" onchange="processFilters()">
                        <option value="all">ALL GENRES</option>
                        <option value="action">ACTION</option>
                        <option value="rpg">RPG</option>
                        <option value="shooter">SHOOTER</option>
                        <option value="sports">SPORTS</option>
                        <option value="strategy">STRATEGY</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>MAXIMUM PRICE CRITERIA: <span id="priceValue" class="neon-pink-text">$85.00</span></label>
                    <input type="range" id="priceRange" class="cyber-slider" min="0" max="100" step="5" value="85" oninput="updatePriceSlider(this.value)" onchange="processFilters()">
                </div>
            </div>
        </section>

        <section class="catalog-feed">
            <div class="catalog-header-bar">
                <h3 class="feed-title">AVAILABLE LICENSE DIRECTORY</h3>
                <span class="feed-count-badge" id="gamesCount">SHOWING {{ count($games) }} SECURE LICENSE INDEXES</span>
            </div>

            <div class="games-grid" id="mainGamesGrid">
                @foreach($games as $game)
                    <div class="game-card" 
                         data-title="{{ strtolower($game['title']) }}" 
                         data-platforms="{{ strtolower($game['platform']) }}"
                         data-genre="{{ strtolower($game['genre']) }}"
                         data-price="{{ $game['price'] }}"
                         data-supports-physical="{{ isset($game['supports_physical']) && $game['supports_physical'] ? 'true' : 'false' }}">
                        
                        <div class="card-art-container">
                            <img src="{{ $game['image'] }}" class="card-img-art" alt="{{ $game['title'] }}">
                            <span class="game-thumbnail-label">{{ $game['genre'] }}</span>
                            @if(isset($game['supports_physical']) && $game['supports_physical'])
                                <span class="physical-badge" title="Physical CD Available">💿</span>
                            @endif
                            <button class="btn-wishlist" onclick="event.preventDefault(); event.stopPropagation(); toggleWishlist({{ $game['id'] }}, this)" data-game-id="{{ $game['id'] }}">🤍</button>
                        </div>
                        <div class="card-details">
                            <div class="card-header-row">
                                <h4 class="game-title">{{ $game['title'] }}</h4>
                            </div>
                            <div class="platform-tags">
                                @foreach(explode(',', $game['platform']) as $plat)
                                    <span class="plat-tag">{{ trim($plat) }}</span>
                                @endforeach
                            </div>
                            <div class="card-purchase-row">
                                <span class="game-price">${{ number_format($game['price'], 2) }}</span>
                                <button class="btn-purchase-action" onclick="viewProductDetails({{ $game['id'] }})">ACQUIRE</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="empty-state-warning" id="noResults" style="display: none;">
                <div class="warning-icon">⚠</div>
                <h4>NO LICENSES MATCH CURRENT PARAMETERS</h4>
                <p>Adjust your filter configuration to locate target software.</p>
                <button class="btn-reset-filters" style="margin-top: 15px;" onclick="resetFilters()">CLEAR FILTERS</button>
            </div>
        </section>
    </div>

    <div class="cart-drawer-overlay" id="cartDrawerOverlay" onclick="toggleCartDrawer()"></div>
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