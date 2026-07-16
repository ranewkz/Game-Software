@extends('layouts.master')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/checkout.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection

@section('content')
    <main class="main-content">
        <div class="checkout-wrapper" id="checkoutLayout">
            <div class="cart-items-container">
                <h3 class="section-header">// SECURE CARGO MANIFEST</h3>
                <div id="checkoutItemsList"></div>
            </div>
            <div class="payment-panel">
                <h3 class="section-header">// AUTHORIZATION</h3>
                <form id="processCheckoutForm">
                    <div class="form-group">
                        <label>DELIVERY TARGET CODES</label>
                        <select id="addressSelect" class="cyber-input">
                            <option value="{{ $user->address ?? 'System Default' }}">PRIMARY: {{ $user->address ?? 'System Default' }}</option>
                            <option value="new">ADD NEW CARGO PATHWAY (SATELLITE MAP)...</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="newAddressGroup" style="display: none; margin-top: 15px;">
                        <label>SELECT SATELLITE DROP ZONE (CLICK MAP TO PIN)</label>
                        <input type="text" id="newAddressInput" class="cyber-input" style="width: 100%; margin-bottom: 10px;" placeholder="Click on the map to set delivery zone..." readonly>
                        <div id="cyberMap" style="height: 300px; width: 100%; border: 1px solid #00f0ff; border-radius: 4px; z-index: 1;"></div>
                    </div>

                    <div class="form-group" style="margin-top: 20px;">
                        <label>PROMOTIONAL OVERRIDE CODE</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="text" id="promoCodeInput" class="cyber-input" style="flex: 1; text-transform: uppercase;" placeholder="ENTER CODE...">
                            <button type="button" id="btnApplyPromo" style="background: transparent; border: 1px solid #00f0ff; color: #00f0ff; padding: 0 20px; font-family: 'Share Tech Mono', monospace; font-weight: 600; cursor: pointer; border-radius: 4px;">APPLY</button>
                        </div>
                        <div id="promoMessage" style="font-size: 0.85rem; margin-top: 8px; font-weight: 600; font-family: 'Share Tech Mono', monospace;"></div>
                    </div>

                    <div class="form-group" style="margin-top: 20px; margin-bottom: 20px;">
                        <label>SECURE PAYMENT UPLINK (CREDIT CARD)</label>
                        <div id="card-element" class="cyber-input" style="padding: 18px; background: rgba(0, 0, 0, 0.7); border: 1px solid rgba(0, 240, 255, 0.2); border-radius: 4px;"></div>
                        <div id="card-errors"></div>
                    </div>
                    <div class="receipt-box">
                        <div class="receipt-line"><span>SUBTOTAL</span><span id="summarySubtotal">$0.00</span></div>
                        <div class="receipt-line"><span>FREIGHT LOGISTICS</span><span id="summaryFreight">$0.00</span></div>
                        <div class="receipt-line" id="discountRow" style="display: none; color: #ff0055;"><span>PROMO DISCOUNT</span><span id="summaryDiscount">-$0.00</span></div>
                        <div class="receipt-line total"><span>TOTAL DEBIT</span><span id="summaryTotal">$0.00</span></div>
                    </div>
                    <button type="submit" id="btnSubmitCheckout" class="btn-authorize">AUTHORIZE TRANSACTION</button>
                </form>
            </div>
        </div>
        <div class="checkout-wrapper" id="emptyStateLayout" style="display: none;">
            <div class="empty-cart-state">
                <h2>YOUR BASKET IS EMPTY</h2>
                <p style="color: #8090a6; font-size: 1.2rem;">You must acquire digital licenses or physical media before accessing the checkout terminal.</p>
                <a href="{{ route('customer.catalog') }}" class="btn-return">RETURN TO CATALOG</a>
            </div>
        </div>
    </main>

    <div class="cyber-receipt-overlay" id="fullReceiptModal">
        <div class="cyber-receipt-paper">
            <div class="receipt-header">
                <h2>STEAM_SYS_RECEIPT</h2>
                <p>TRANSACTION LOG & ACTIVATION KEYS</p>
            </div>
            <div class="receipt-meta">
                <div>ORDER ID:</div><div id="rec-id"></div>
                <div>DATE:</div><div id="rec-date"></div>
                <div>DESTINATION:</div><div id="rec-address"></div>
            </div>
            <div class="receipt-divider"></div>
            <div id="rec-keys"></div>
            <div class="receipt-divider"></div>
            <div class="receipt-totals">
                <div class="r-row"><span>SUBTOTAL</span><span id="rec-sub"></span></div>
                <div class="r-row"><span>FREIGHT</span><span id="rec-freight"></span></div>
                <div class="r-row r-grand"><span>TOTAL DEBIT</span><span id="rec-total"></span></div>
            </div>
            <div class="receipt-footer">
                <div class="barcode">||| |||| ||| | ||||| |||| ||</div>
                <p>AUTHORIZED BY STEAM CORE</p>
                <button class="btn-receipt-action" id="vaultRedirectBtn">ACCESS SECURE VAULT</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        window.stripeKey = "{{ env('STRIPE_KEY') }}";
        window.checkoutProcessUrl = "{{ route('checkout.process') }}";
        window.myOrdersUrl = "{{ route('my.orders') }}";
        window.checkPromoUrl = "{{ route('checkout.check-promo') }}";
    </script>
    <script src="{{ asset('js/checkout.js') }}?v={{ time() }}"></script>
@endsection