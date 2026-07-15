@extends('layouts.master')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/orders.css') }}?v={{ time() }}">
@endsection

@section('content')
    <main class="main-content">
        <div class="orders-wrapper">
            <div class="orders-header-row">
                <h2 class="section-header">// LOGISTICS & PURCHASE ARCHIVE</h2>
                <div class="order-filters">
                    <button class="filter-btn active" data-filter="all">ALL LOGS</button>
                    <button class="filter-btn" data-filter="pending">PENDING</button>
                    <button class="filter-btn" data-filter="shipped">SHIPPED</button>
                    <button class="filter-btn" data-filter="delivered">DELIVERED</button>
                </div>
            </div>

            <div class="orders-list">
                @if(isset($orders) && count($orders) > 0)
                    @foreach($orders as $order)
                        <div class="order-card" data-status="{{ strtolower($order->status) }}">
                            <div class="order-card-header">
                                <div class="order-id">
                                    <span class="label">ORDER ID:</span>
                                    <span class="value">#STM-{{ $order->id }}</span>
                                </div>
                                <div class="order-status status-{{ strtolower($order->status) }}">
                                    {{ strtoupper($order->status) }}
                                </div>
                            </div>

                            <div class="order-card-body">
                                <div class="detail-column">
                                    <span class="detail-label">TRANSACTION DATE</span>
                                    <span class="detail-value">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }}</span>
                                </div>
                                <div class="detail-column">
                                    <span class="detail-label">PAYMENT GATEWAY</span>
                                    <span class="detail-value">System Credits</span>
                                </div>
                                <div class="detail-column">
                                    <span class="detail-label">CARGO PATHWAY</span>
                                    <span class="detail-value">{{ $order->shipping_address ?? 'Digital Delivery' }}</span>
                                </div>
                                <div class="detail-column">
                                    <span class="detail-label">TOTAL CHARGED</span>
                                    <span class="detail-value neon-price">${{ number_format($order->total_amount, 2) }}</span>
                                </div>
                            </div>

                            <div class="order-card-footer">
                                <button class="btn-action btn-track" 
                                    data-id="STM-{{ $order->id }}"
                                    data-date="{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }}"
                                    data-address="{{ $order->shipping_address ?? 'Digital Delivery' }}"
                                    data-total="{{ $order->total_amount }}"
                                    data-items="{{ htmlspecialchars($order->items_summary, ENT_QUOTES, 'UTF-8') }}"
                                    onclick="openOrderReceipt(this)">
                                    VIEW RECEIPT
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <div class="empty-icon">📁</div>
                        <h3>NO LOGISTICS RECORDS FOUND</h3>
                        <p>Your transaction history is currently empty.</p>
                        <br><br>
                        <a href="{{ route('customer.catalog') }}" class="btn-primary" style="padding: 10px 20px; text-decoration: none;">BROWSE CATALOG</a>
                    </div>
                @endif
            </div>
        </div>
    </main>

    <div class="cyber-receipt-overlay" id="fullReceiptModal">
        <div class="cyber-receipt-paper">
            <div class="receipt-header">
                <h2>// STEAM_LOG</h2>
                <p>SECURE TRANSACTION</p>
            </div>
            
            <div class="receipt-meta">
                <p>ORDER ID: <span id="rec-id"></span></p>
                <p>TIMESTAMP: <span id="rec-date"></span></p>
                <p>PAYMENT: <span>SYSTEM CREDITS</span></p>
                <p>DESTINATION: <span id="rec-address"></span></p>
            </div>
            
            <div class="receipt-divider"></div>
            
            <div class="receipt-items-table">
                <div class="r-row r-head">
                    <span class="r-item-title">ITEM MANIFEST</span>
                    <span>SUBTOTAL</span>
                </div>
                <div id="rec-items"></div>
            </div>
            
            <div class="receipt-divider"></div>
            
            <div class="receipt-totals">
                <div class="r-row">
                    <span class="r-item-title">LOGISTICS FREIGHT</span>
                    <span class="r-item-price" id="rec-freight"></span>
                </div>
                <div class="r-row r-grand">
                    <span class="r-item-title">TOTAL DEBIT</span>
                    <span id="rec-total"></span>
                </div>
            </div>
            
            <div class="receipt-footer">
                <div class="barcode">STEAMCORE</div>
                <p>AUTHORIZED BY STEAM SYSTEM</p>
                <button class="btn-receipt-action" onclick="closeOrderReceipt()">CLOSE LOG</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/orders.js') }}?v={{ time() }}"></script>
@endsection