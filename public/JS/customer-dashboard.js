let shoppingCart = [];
let currentSliderIndex = 0;
let sliderTimer = null;

let customerOrders = [
    {
        id: "STM-91823",
        date: "2026-06-01",
        items: [{ title: "Cyberpunk 2077: Ultimate Edition", format: "digital", price: 49.99, qty: 1 }],
        payment: "Kpay",
        address: window.userConfig.primaryAddress,
        status: "shipped",
        total: 49.99
    },
    {
        id: "STM-82941",
        date: "2026-05-15",
        items: [{ title: "Marvel's Spider-Man 2", format: "physical", price: 69.99, qty: 1 }],
        payment: "Cash on Delivery",
        address: window.userConfig.primaryAddress,
        status: "delivered",
        total: 81.99
    }
];

window.onload = function() {
    startHeroSliderTimer();
    renderCartDrawerItems();
    processFilters();
};

function startHeroSliderTimer() {
    if (sliderTimer) clearInterval(sliderTimer);
    sliderTimer = setInterval(function() {
        setSliderSlide((currentSliderIndex + 1) % 3);
    }, 6000);
}

function setSliderSlide(index) {
    const slides = document.querySelectorAll('.slider-slide');
    const dots = document.querySelectorAll('.slider-dot');
    if (slides.length === 0) return;
    currentSliderIndex = index;
    slides.forEach((slide, i) => i === index ? slide.classList.add('active') : slide.classList.remove('active'));
    dots.forEach((dot, i) => i === index ? dot.classList.add('active') : dot.classList.remove('active'));
}

function scrollToCatalogAndFilter(genre) {
    const select = document.getElementById('genreFilter');
    if (select) { select.value = genre; processFilters(); }
    document.getElementById('catalogSection')?.scrollIntoView({ behavior: 'smooth' });
}

function scrollToCatalogAndSearch(keyword) {
    const input = document.getElementById('searchGame');
    if (input) { input.value = keyword; processFilters(); }
    document.getElementById('catalogSection')?.scrollIntoView({ behavior: 'smooth' });
}

function scrollToVault() {
    document.getElementById('cdVaultSection')?.scrollIntoView({ behavior: 'smooth' });
}

function updatePriceSlider(val) {
    const label = document.getElementById('priceValue');
    if (label) label.innerText = '$' + parseFloat(val).toFixed(2);
}

function processFilters() {
    const searchVal = document.getElementById('searchGame').value.toLowerCase();
    const deviceVal = document.getElementById('deviceFilter').value.toLowerCase();
    const formatVal = document.getElementById('formatFilter').value.toLowerCase();
    const genreVal = document.getElementById('genreFilter').value.toLowerCase();
    const priceLimit = parseFloat(document.getElementById('priceRange').value);

    const cards = document.querySelectorAll('.game-card');
    let matchedCount = 0;

    cards.forEach(card => {
        const title = card.getAttribute('data-title') || '';
        const platforms = card.getAttribute('data-platforms') || '';
        const genre = card.getAttribute('data-genre') || '';
        const basePrice = parseFloat(card.getAttribute('data-price') || '0');
        const supportsPhysical = card.getAttribute('data-supports-physical') === 'true';

        let matchesFormat = formatVal === 'all' || (formatVal === 'physical' && supportsPhysical) || (formatVal === 'digital');
        const matchesSearch = title.includes(searchVal);
        const matchesDevice = deviceVal === 'all' || platforms.includes(deviceVal);
        const matchesGenre = genreVal === 'all' || genre === genreVal;
        const matchesPrice = basePrice <= priceLimit;

        if (matchesSearch && matchesDevice && matchesFormat && matchesGenre && matchesPrice) {
            card.style.display = 'flex';
            matchedCount++;
        } else {
            card.style.display = 'none';
        }
    });

    const label = document.getElementById('gamesCount');
    const noResults = document.getElementById('noResults');
    if (label && noResults) {
        if (matchedCount === 0) { noResults.style.display = 'flex'; label.innerText = "SHOWING 0 MATCHES"; }
        else { noResults.style.display = 'none'; label.innerText = `SHOWING ${matchedCount} SECURE LICENSE INDEXES`; }
    }
}

function resetFilters() {
    document.getElementById('searchGame').value = '';
    document.getElementById('deviceFilter').value = 'ALL';
    document.getElementById('formatFilter').value = 'ALL';
    document.getElementById('genreFilter').value = 'ALL';
    document.getElementById('priceRange').value = '85';
    updatePriceSlider(85);
    processFilters();
}

function viewProductDetails(gameId, preselectedFormat = 'digital') {
    const game = window.gamesDatabase.find(g => parseInt(g.id) === parseInt(gameId));
    if (!game) return;

    const modal = document.getElementById('productModal');
    const content = document.getElementById('productModalContent');
    const isOutOfStock = parseInt(game.stock) === 0;

    let formatOptionHtml = game.supports_physical ? `
        <div class="format-selector-row">
            <span class="format-label">SELECT SPEC EDITION FORMAT:</span>
            <div class="format-options">
                <label class="format-toggle">
                    <input type="radio" name="modalFormat" value="digital" ${preselectedFormat === 'digital' ? 'checked' : ''} onchange="updateModalPrice(${game.price})">
                    <span class="toggle-btn">DIGITAL LICENSE</span>
                </label>
                <label class="format-toggle">
                    <input type="radio" name="modalFormat" value="physical" ${preselectedFormat === 'physical' ? 'checked' : ''} onchange="updateModalPrice(${game.price + 12})">
                    <span class="toggle-btn disc-toggle">PHYSICAL CD</span>
                </label>
            </div>
        </div>` : `
        <div class="format-selector-row">
            <span class="format-label">FORMAT COMPATIBILITY:</span>
            <span class="plat-tag">DIGITAL LICENSE EXCLUSIVE</span>
            <input type="hidden" name="modalFormat" value="digital">
        </div>`;

    content.innerHTML = `
        <div class="modal-product-visuals">
            <img src="${game.image}" class="modal-product-img">
            <div class="modal-specs-box">
                <div class="spec-line"><span>INDEX REGISTRATION ID:</span><span>#STM-G${game.id}</span></div>
                <div class="spec-line"><span>ALLOCATED UNITS:</span><strong class="${isOutOfStock ? 'out-of-stock-txt' : 'in-stock-txt'}">${game.stock} IN STOCK</strong></div>
            </div>
        </div>
        <div class="modal-product-info">
            <span class="modal-genre-tag">${game.genre}</span>
            <h2 class="modal-product-title">${game.title}</h2>
            <p class="modal-desc-text">${game.description}</p>
            <div class="modal-format-box">
                ${formatOptionHtml}
                <div class="spec-line divider-top" style="font-size: 1.3rem; font-weight: bold;">
                    <span>SUBTOTAL PRICE:</span><span id="modalPriceLabel">$${(preselectedFormat === 'physical' ? game.price + 12 : game.price).toFixed(2)}</span>
                </div>
            </div>
            <button class="btn-modal-add-cart" onclick="addGameToBasket(${game.id})" ${isOutOfStock ? 'disabled style="background: #333; color: #888; cursor: not-allowed;"' : ''}>
                ${isOutOfStock ? 'TEMPORARILY OUT OF WAREHOUSE' : 'STASH IN SECURE BASKET'}
            </button>
        </div>`;
    modal.style.display = 'flex';
}

function updateModalPrice(price) {
    document.getElementById('modalPriceLabel').innerText = '$' + parseFloat(price).toFixed(2);
}

function closeProductModal() { document.getElementById('productModal').style.display = 'none'; }

function toggleCartDrawer() {
    document.getElementById('cartDrawer').classList.toggle('active');
    document.getElementById('cartDrawerOverlay').classList.toggle('active');
}

function addGameToBasket(gameId) {
    const game = window.gamesDatabase.find(g => parseInt(g.id) === parseInt(gameId));
    if (!game || game.stock <= 0) return;

    const formatMode = document.querySelector('input[name="modalFormat"]:checked')?.value || 'digital';
    const existing = shoppingCart.find(it => it.id === game.id && it.format === formatMode);

    if (existing) {
        if (existing.qty + 1 > game.stock) { alert("Cart item limits exceed warehouse allocation counts."); return; }
        existing.qty++;
    } else {
        shoppingCart.push({ id: game.id, title: game.title, image: game.image, price: game.price, format: formatMode, qty: 1, maxStock: game.stock });
    }
    closeProductModal(); renderCartDrawerItems(); toggleCartDrawer();
}

function renderCartDrawerItems() {
    const tray = document.getElementById('cartItemsTray');
    const badge = document.getElementById('cartGlobalCount');
    if (!tray || !badge) return;

    if (shoppingCart.length === 0) {
        tray.innerHTML = `<div class="empty-basket-container"><div class="empty-basket-icon">📦</div><h4>YOUR BASKET IS EMPTY</h4></div>`;
        badge.innerText = '0'; updateCartTotalSummary(0, 0, 0); return;
    }

    let subtotal = 0, totalQty = 0, freightFee = 0, itemsHtml = '';
    shoppingCart.forEach((item, idx) => {
        const itemCost = item.format === 'physical' ? item.price + 12 : item.price;
        subtotal += item.price * item.qty; totalQty += item.qty;
        if (item.format === 'physical') freightFee += 12 * item.qty;

        itemsHtml += `
            <div class="cart-item-card">
                <img src="${item.image}" class="cart-item-thumb">
                <div class="cart-item-details">
                    <span class="cart-item-title">${item.title}</span>
                    <span class="cart-item-price">$${itemCost.toFixed(2)}</span>
                </div>
                <div class="quantity-controls">
                    <button class="btn-qty-adj" onclick="adjustCartQty(${idx}, -1)">&minus;</button>
                    <span class="qty-val-indicator">${item.qty}</span>
                    <button class="btn-qty-adj" onclick="adjustCartQty(${idx}, 1)">&plus;</button>
                </div>
                <button class="btn-cart-remove" onclick="removeCartItem(${idx})">&times;</button>
            </div>`;
    });

    tray.innerHTML = itemsHtml; badge.innerText = totalQty;
    updateCartTotalSummary(subtotal, freightFee, subtotal + freightFee);
}

function adjustCartQty(index, dir) {
    const item = shoppingCart[index];
    if (dir === 1 && item.qty + 1 > item.maxStock) return;
    item.qty += dir;
    if (item.qty <= 0) shoppingCart.splice(index, 1);
    renderCartDrawerItems();
}

function removeCartItem(index) { shoppingCart.splice(index, 1); renderCartDrawerItems(); }

function updateCartTotalSummary(sub, freight, total) {
    document.getElementById('cartSubtotal').innerText = '$' + sub.toFixed(2);
    document.getElementById('cartFreightFee').innerText = '$' + freight.toFixed(2);
    document.getElementById('cartTotal').innerText = '$' + total.toFixed(2);
}

function openCheckoutProcess() {
    if (shoppingCart.length === 0) return;
    toggleCartDrawer();
    const addressSelect = document.getElementById('checkoutAddressSelect');
    let options = '';
    window.userConfig.savedAddresses.forEach(addr => options += `<option value="${addr}">${addr}</option>`);
    options += `<option value="new">// DEPLOY NEW ADDRESS Coordinates</option>`;
    addressSelect.innerHTML = options;

    let subtotal = 0, freight = 0;
    shoppingCart.forEach(it => { subtotal += it.price * it.qty; if (it.format === 'physical') freight += 12 * it.qty; });
    document.getElementById('checkoutCostBreakdown').innerHTML = `<div class="invoice-row"><span>GRAND TOTAL CHARGE:</span><strong>$${(subtotal + freight).toFixed(2)}</strong></div>`;
    document.getElementById('checkoutModal').style.display = 'flex';
}

function toggleNewAddressInput(val) { document.getElementById('newAddressGroup').style.display = val === 'new' ? 'flex' : 'none'; }
function closeCheckoutModal() { document.getElementById('checkoutModal').style.display = 'none'; }

function submitCheckout(e) {
    e.preventDefault();
    let addr = document.getElementById('checkoutAddressSelect').value;
    if (addr === 'new') addr = document.getElementById('newAddressInput').value;
    
    const id = "STM-" + Math.floor(100000 + Math.random() * 900000);
    let sub = 0, freight = 0;
    shoppingCart.forEach(it => { sub += it.price * it.qty; if (it.format === 'physical') freight += 12 * it.qty; });

    const order = { id, date: new Date().toISOString().split('T')[0], items: [...shoppingCart], payment: "Kpay", address: addr, status: "pending", total: sub + freight };
    customerOrders.unshift(order);
    closeCheckoutModal(); openInvoiceConfirmation(order); shoppingCart = []; renderCartDrawerItems();
}

function openInvoiceConfirmation(order) {
    document.getElementById('invoiceId').innerText = `// ORDER ID: ${order.id}`;
    document.getElementById('invoiceCargoPathway').innerText = order.address;
    document.getElementById('invoicePaymentType').innerText = order.payment;
    document.getElementById('invoiceFinalAmount').innerText = `$${order.total.toFixed(2)}`;
    document.getElementById('invoiceModal').style.display = 'flex';
}

function closeInvoiceModal() { document.getElementById('invoiceModal').style.display = 'none'; }

function openOrdersModal() {
    let html = '';
    customerOrders.forEach(o => {
        html += `<div class="order-history-card"><strong>ID: ${o.id}</strong> - Status: ${o.status.toUpperCase()}<br>Total: $${o.total.toFixed(2)}</div>`;
    });
    document.getElementById('ordersHistoryContainer').innerHTML = html;
    document.getElementById('ordersModal').style.display = 'flex';
}
function closeOrdersModal() { document.getElementById('ordersModal').style.display = 'none'; }
function openProfileModal() { document.getElementById('profileModal').style.display = 'flex'; }
function closeProfileModal() { document.getElementById('profileModal').style.display = 'none'; }