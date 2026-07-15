// ==========================================
// 1. GLOBAL VARIABLES & INITIALIZATION
// ==========================================
let shoppingCart = JSON.parse(localStorage.getItem('steam_cart')) || [];
let wishlistItems = [];
let currentSliderIndex = 0;
let sliderTimer = null;

window.onload = function() {
    startHeroSliderTimer();
    renderCartDrawerItems();
    fetchWishlist();
    
    if (document.getElementById('searchGame')) {
        processFilters();
    }
};

// ==========================================
// 2. HERO SLIDER LOGIC
// ==========================================
function startHeroSliderTimer() {
    if (sliderTimer) clearInterval(sliderTimer);
    sliderTimer = setInterval(function() {
        setSliderSlide((currentSliderIndex + 1) % 2); 
    }, 6000);
}

function setSliderSlide(index) {
    const slides = document.querySelectorAll('.slider-slide');
    const dots = document.querySelectorAll('.slider-dot');
    if (slides.length === 0) return;
    
    currentSliderIndex = index;
    
    slides.forEach((slide, i) => {
        i === index ? slide.classList.add('active') : slide.classList.remove('active');
    });
    
    dots.forEach((dot, i) => {
        i === index ? dot.classList.add('active') : dot.classList.remove('active');
    });
}

// ==========================================
// 3. CATALOG FILTER LOGIC
// ==========================================
function updatePriceSlider(val) {
    const label = document.getElementById('priceValue');
    if (label) label.innerText = '$' + parseFloat(val).toFixed(2);
}

function processFilters() {
    const searchInput = document.getElementById('searchGame');
    if (!searchInput) return; 

    const searchVal = searchInput.value.toLowerCase();
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

        let matchesFormat = formatVal === 'all' || 
                            (formatVal === 'physical' && supportsPhysical) || 
                            (formatVal === 'digital');
                            
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
        if (matchedCount === 0) { 
            noResults.style.display = 'flex'; 
            label.innerText = "SHOWING 0 MATCHES"; 
        } else { 
            noResults.style.display = 'none'; 
            label.innerText = `SHOWING ${matchedCount} SECURE LICENSE INDEXES`; 
        }
    }
}

function resetFilters() {
    if (!document.getElementById('searchGame')) return;
    document.getElementById('searchGame').value = '';
    document.getElementById('deviceFilter').value = 'all';
    document.getElementById('formatFilter').value = 'all';
    document.getElementById('genreFilter').value = 'all';
    document.getElementById('priceRange').value = '85';
    updatePriceSlider(85);
    processFilters();
}

// ==========================================
// 4. MODAL POPUP LOGIC (THE "VIEW" BUTTON)
// ==========================================
function viewProductDetails(gameId, preselectedFormat = 'digital') {
    
    if (!window.gamesDatabase) {
        console.error("CRITICAL: Database not loaded into window.");
        return;
    }

    const dbArray = Array.isArray(window.gamesDatabase) ? window.gamesDatabase : Object.values(window.gamesDatabase);
    const game = dbArray.find(g => parseInt(g.id) === parseInt(gameId));
    if (!game) return;

    const modal = document.getElementById('productModal');
    const content = document.getElementById('productModalContent');

    const isPhysical = game.supports_physical == 1 || game.supports_physical === true || game.supports_physical === '1';

    // Extract additional data (with fallbacks if db is empty)
    const platformHtml = game.platform ? game.platform.split(',').map(p => `<span class="plat-tag">${p.trim()}</span>`).join('') : '<span class="plat-tag">UNIVERSAL</span>';
    const description = game.description || "Highly classified digital asset. Secure your license to access full operational capabilities and tactical deployments in this sector.";
    const rating = game.rating && game.rating !== "N/A" ? game.rating : "9.5/10 (CRITICAL ACCLAIM)";
    const stockStr = game.stock > 0 ? `${game.stock} UNITS IN VAULT` : 'UNLIMITED DIGITAL LICENSES';
    
    // Safety check: If stock is 0 but it's digital, we allow a high limit (99). If physical, we strictly use DB stock.
    const maxStock = game.stock > 0 ? game.stock : 99;

    let formatOptionHtml = isPhysical ? `
        <div class="format-selector-row">
            <span class="format-label">SELECT SPEC EDITION FORMAT:</span>
            <div class="format-options">
                <label class="format-toggle">
                    <input type="radio" name="modalFormat" value="digital" ${preselectedFormat === 'digital' ? 'checked' : ''} onchange="updateModalPrice(${game.price})">
                    <span class="toggle-btn">DIGITAL CODE</span>
                </label>
                <label class="format-toggle">
                    <input type="radio" name="modalFormat" value="physical" ${preselectedFormat === 'physical' ? 'checked' : ''} onchange="updateModalPrice(${game.price + 12})">
                    <span class="toggle-btn disc-toggle">PHYSICAL CD (+ $12 FREIGHT)</span>
                </label>
            </div>
        </div>` : `<input type="hidden" name="modalFormat" value="digital">`;

    content.innerHTML = `
        <div class="product-details-grid">
            <div class="modal-product-visuals">
                <div class="modal-img-wrapper">
                    <img src="${game.image}" class="modal-product-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="fallback-img-box" style="display: none;">[ ENCRYPTED VISUAL ASSET ]</div>
                </div>
            </div>
            
            <div class="modal-product-info">
                <div class="modal-meta-header">
                    <span class="modal-genre-tag">${game.genre}</span>
                    <span class="modal-stock-status">STATUS: ${stockStr}</span>
                </div>
                
                <h2 class="modal-product-title">${game.title}</h2>
                <div class="modal-platform-tags">${platformHtml}</div>
                
                <p class="modal-description-text">> ${description}</p>
                
                <div class="modal-stats-grid">
                    <div class="stat-box">
                        <span>USER RATING</span>
                        <strong>${rating}</strong>
                    </div>
                    <div class="stat-box">
                        <span>REGION LOCK</span>
                        <strong style="color: var(--neon-cyan);">GLOBAL UNRESTRICTED</strong>
                    </div>
                </div>

                <div class="modal-format-box">
                    ${formatOptionHtml}
                    
                    <div class="modal-qty-row">
                        <span style="color: #888; font-size: 0.9rem; text-transform: uppercase; font-weight: bold;">SELECT QUANTITY:</span>
                        <div class="qty-controls">
                            <button class="btn-qty" onclick="updateModalQty(-1, ${maxStock}, ${game.price})">-</button>
                            <input type="number" id="modalQty" class="input-qty" value="1" min="1" max="${maxStock}" readonly>
                            <button class="btn-qty" onclick="updateModalQty(1, ${maxStock}, ${game.price})">+</button>
                        </div>
                    </div>

                    <div class="spec-line" style="font-size: 1.4rem; font-weight: 900;">
                        <span style="color: #888; font-size: 1rem; text-transform: uppercase;">Total Cost:</span>
                        <span id="modalPriceLabel" style="color: var(--neon-cyan);">$${(preselectedFormat === 'physical' ? parseFloat(game.price) + 12 : parseFloat(game.price)).toFixed(2)}</span>
                    </div>
                </div>
                
                <button class="btn-modal-add-cart" id="btnAddToCart" onclick="addGameToBasket(${game.id})">
                    AUTHORIZE STASH TO BASKET
                </button>
            </div>
        </div>`;
        
    modal.style.display = 'flex';
}

function updateModalQty(change, maxStock, basePrice) {
    const qtyInput = document.getElementById('modalQty');
    let currentQty = parseInt(qtyInput.value);
    let newQty = currentQty + change;
    
    // Prevent going below 1
    if (newQty < 1) newQty = 1;
    
    // Check against Database Stock Limit
    if (newQty > maxStock) {
        newQty = maxStock;
        
        // Cyberpunk visual feedback instead of annoying browser alert
        const btn = document.getElementById('btnAddToCart');
        const origText = btn.innerText;
        btn.innerText = "MAX INVENTORY LIMIT REACHED";
        btn.style.color = "#ff003c";
        btn.style.borderColor = "#ff003c";
        btn.style.boxShadow = "0 0 20px rgba(255, 0, 60, 0.4)";
        
        setTimeout(() => {
            btn.innerText = origText;
            btn.style.color = "var(--neon-pink, #ff007c)";
            btn.style.borderColor = "var(--neon-pink, #ff007c)";
            btn.style.boxShadow = "0 0 15px rgba(255, 0, 124, 0.15)";
        }, 1500);
    }
    
    qtyInput.value = newQty;
    
    // Recalculate the price dynamically based on format and quantity
    const isPhysical = document.querySelector('input[name="modalFormat"]:checked')?.value === 'physical';
    const unitPrice = isPhysical ? basePrice + 12 : basePrice;
    updateModalPrice(unitPrice);
}

function updateModalPrice(unitPrice) { 
    const qty = parseInt(document.getElementById('modalQty')?.value || 1);
    document.getElementById('modalPriceLabel').innerText = '$' + (parseFloat(unitPrice) * qty).toFixed(2); 
}

function closeProductModal() { 
    document.getElementById('productModal').style.display = 'none'; 
}

// ==========================================
// 5. CART LOGIC
// ==========================================
function toggleCartDrawer() {
    document.getElementById('cartDrawer').classList.toggle('active');
    document.getElementById('cartDrawerOverlay').classList.toggle('active');
}

function addGameToBasket(gameId) {
    const dbArray = Array.isArray(window.gamesDatabase) ? window.gamesDatabase : Object.values(window.gamesDatabase);
    const game = dbArray.find(g => parseInt(g.id) === parseInt(gameId));
    
    const formatMode = document.querySelector('input[name="modalFormat"]:checked')?.value || 'digital';
    const qtyToAdd = parseInt(document.getElementById('modalQty').value); // Reads the user's selected quantity!
    const maxStock = game.stock > 0 ? game.stock : 99;
    
    const existing = shoppingCart.find(it => parseInt(it.id) === parseInt(game.id) && it.format === formatMode);

    if (existing) {
        // Double check stock again if they already have some in their cart!
        if (existing.qty + qtyToAdd > maxStock) {
            existing.qty = maxStock;
        } else {
            existing.qty += qtyToAdd;
        }
    } else {
        shoppingCart.push({ id: game.id, title: game.title, image: game.image, price: parseFloat(game.price), format: formatMode, qty: qtyToAdd });
    }
    
    // SAVE TO LOCAL STORAGE SO IT SURVIVES PAGE REFRESHES
    localStorage.setItem('steam_cart', JSON.stringify(shoppingCart));
    
    closeProductModal(); 
    renderCartDrawerItems(); 
    toggleCartDrawer();
}

function renderCartDrawerItems() {
    const tray = document.getElementById('cartItemsTray');
    const badge = document.getElementById('cartGlobalCount');
    if (!tray || !badge) return;

    if (shoppingCart.length === 0) {
        tray.innerHTML = `<div style="text-align:center; padding:30px; color:#888;">YOUR BASKET IS EMPTY</div>`;
        badge.innerText = '0'; 
        document.getElementById('cartSubtotal').innerText = '$0.00'; 
        document.getElementById('cartFreightFee').innerText = '$0.00'; 
        document.getElementById('cartTotal').innerText = '$0.00'; 
        return;
    }

    let subtotal = 0, totalQty = 0, freightFee = 0, itemsHtml = '';
    
    shoppingCart.forEach((item, idx) => {
        const itemCost = item.format === 'physical' ? item.price + 12 : item.price;
        subtotal += item.price * item.qty; 
        totalQty += item.qty;
        if (item.format === 'physical') freightFee += 12 * item.qty;

        itemsHtml += `
            <div class="cart-item-card">
                <img src="${item.image}" class="cart-item-thumb">
                <div class="cart-item-details">
                    <span class="cart-item-title">${item.title}</span>
                    <span class="cart-item-price">$${itemCost.toFixed(2)}</span>
                </div>
                <button class="btn-cart-remove" onclick="removeCartItem(${idx})">&times;</button>
            </div>`;
    });

    tray.innerHTML = itemsHtml; 
    badge.innerText = totalQty;
    document.getElementById('cartSubtotal').innerText = '$' + subtotal.toFixed(2);
    document.getElementById('cartFreightFee').innerText = '$' + freightFee.toFixed(2);
    document.getElementById('cartTotal').innerText = '$' + (subtotal + freightFee).toFixed(2);
}

function removeCartItem(index) { 
    shoppingCart.splice(index, 1); 
    // UPDATE LOCAL STORAGE ON REMOVAL
    localStorage.setItem('steam_cart', JSON.stringify(shoppingCart));
    renderCartDrawerItems(); 
}

// ==========================================
// 6. CHECKOUT & DATABASE COMMUNICATION
// ==========================================
function openCheckoutProcess() {
    if (shoppingCart.length === 0) return;
    // REDIRECT TO THE NEW CHECKOUT PAGE INSTEAD OF OPENING A MODAL
    window.location.href = '/checkout';
}

function toggleNewAddressInput(val) { 
    document.getElementById('newAddressGroup').style.display = val === 'new' ? 'flex' : 'none'; 
}

function closeCheckoutModal() { 
    document.getElementById('checkoutModal').style.display = 'none'; 
}

async function submitCheckout(e) {
    e.preventDefault();
    
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerText;
    submitBtn.innerText = "AUTHORIZING...";
    submitBtn.disabled = true;

    const addressSelect = document.getElementById('checkoutAddressSelect').value;
    const finalAddress = addressSelect === 'new' ? document.getElementById('newAddressInput').value : addressSelect;

    try {
        const response = await fetch('/checkout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || ''
            },
            body: JSON.stringify({ cart: shoppingCart, address: finalAddress })
        });

        const data = await response.json();

        if (response.ok && data.success) {
            document.getElementById('walletBalance').innerText = '$' + data.new_credits.toFixed(2);
            closeCheckoutModal(); 
            document.getElementById('invoiceModal').style.display = 'flex'; 
            shoppingCart = []; 
            renderCartDrawerItems();
        } else {
            alert('TRANSACTION FAILED: ' + (data.error || 'System Error'));
        }
    } catch (error) {
        console.error('Checkout error:', error);
        alert('NETWORK ERROR: Could not reach the server.');
    } finally {
        submitBtn.innerText = originalText;
        submitBtn.disabled = false;
    }
}

window.toggleWishlistDrawer = function() {
    document.getElementById('wishlistDrawer').classList.toggle('active');
    document.getElementById('cartDrawerOverlay').classList.toggle('active');
    if (document.getElementById('wishlistDrawer').classList.contains('active')) {
        document.getElementById('cartDrawer').classList.remove('active');
    }
};

window.injectWishlistButtons = function() {
    document.querySelectorAll('.btn-purchase-action, .btn-vault-buy').forEach(btn => {
        const card = btn.closest('.game-card, .vault-card');
        if (!card) return;
        
        const artContainer = card.querySelector('.card-art-container, .box-art-depth');
        if (!artContainer) return;
        if (artContainer.querySelector('.btn-wishlist')) return;

        const gameIdMatch = btn.getAttribute('onclick').match(/\d+/);
        if (!gameIdMatch) return;
        const gameId = parseInt(gameIdMatch[0]);
        
        const heartBtn = document.createElement('button');
        heartBtn.className = 'btn-wishlist';
        heartBtn.setAttribute('data-game-id', gameId);
        heartBtn.innerHTML = '🤍';
        heartBtn.onclick = (e) => { e.preventDefault(); e.stopPropagation(); window.toggleWishlist(gameId, heartBtn); };
        
        artContainer.appendChild(heartBtn);
    });
};

window.fetchWishlist = async function() {
    try {
        const res = await fetch('/wishlist/get');
        const data = await res.json();
        // Force all IDs to be clean numbers
        wishlistItems = data.map(id => parseInt(id));
        updateWishlistUI();
    } catch (e) {
        console.error("Failed to load wishlist");
    }
};

window.showToast = function(message, isAdded) {
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        document.body.appendChild(toastContainer);
    }

    const toast = document.createElement('div');
    toast.className = 'cyber-toast';
    const color = isAdded ? 'var(--neon-cyan)' : 'var(--neon-pink)';
    toast.style.borderLeftColor = color;
    toast.style.color = color;
    toast.innerHTML = `> ${message}`;

    toastContainer.appendChild(toast);

    setTimeout(() => toast.classList.add('show'), 10);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 400);
    }, 3000);
};

window.toggleWishlist = async function(gameId, clickedBtn) {
    if(clickedBtn) {
        clickedBtn.style.transform = 'scale(1.3)';
        setTimeout(() => clickedBtn.style.transform = '', 200);
    }

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        
        const res = await fetch('/wishlist/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ game_id: parseInt(gameId) })
        });
        
        if (!res.ok) {
            window.showToast('NETWORK ERROR ' + res.status + ': COULD NOT SYNC', false);
            return;
        }

        const data = await res.json();
        const parsedId = parseInt(gameId);
        
        if (data.status === 'added') {
            if (!wishlistItems.includes(parsedId)) wishlistItems.push(parsedId);
            window.showToast('SYSTEM: ACQUIRED TO WISHLIST', true);
        } else {
            wishlistItems = wishlistItems.filter(id => id !== parsedId);
            window.showToast('SYSTEM: REMOVED FROM WISHLIST', false);
        }
        
        updateWishlistUI();
    } catch (e) {
        window.showToast('SYSTEM FAULT: UNREACHABLE', false);
    }
};

window.updateWishlistUI = function() {
    document.getElementById('wishlistGlobalCount').innerText = wishlistItems.length;
    
    document.querySelectorAll('.btn-wishlist').forEach(btn => {
        const gameId = parseInt(btn.getAttribute('data-game-id'));
        if (wishlistItems.includes(gameId)) {
            btn.innerHTML = '💖';
            btn.classList.add('active');
        } else {
            btn.innerHTML = '🤍';
            btn.classList.remove('active');
        }
    });

    const tray = document.getElementById('wishlistItemsTray');
    if (!tray) return;

    if (wishlistItems.length === 0) {
        tray.innerHTML = `<div style="text-align:center; padding:30px; color:#888;">WISHLIST IS EMPTY</div>`;
        return;
    }

    const dbArray = Array.isArray(window.gamesDatabase) ? window.gamesDatabase : Object.values(window.gamesDatabase);
    let html = '';
    wishlistItems.forEach(id => {
        const game = dbArray.find(g => parseInt(g.id) === parseInt(id));
        if (game) {
            html += `
            <div class="cart-item-card" style="border-left: 3px solid var(--neon-pink)">
                <img src="${game.image}" class="cart-item-thumb">
                <div class="cart-item-details">
                    <span class="cart-item-title">${game.title}</span>
                    <span class="cart-item-price">$${parseFloat(game.price).toFixed(2)}</span>
                </div>
                <button class="btn-cart-remove" style="color:var(--neon-pink)" onclick="toggleWishlist(${game.id})">&times;</button>
            </div>`;
        }
    });
    tray.innerHTML = html;
};

window.viewProductDetails = viewProductDetails;
window.closeProductModal = closeProductModal;
window.updateModalPrice = updateModalPrice;
window.updateModalQty = updateModalQty;
window.addGameToBasket = addGameToBasket;
window.toggleCartDrawer = toggleCartDrawer;
window.removeCartItem = removeCartItem;
window.openCheckoutProcess = openCheckoutProcess;
window.toggleNewAddressInput = toggleNewAddressInput;
window.closeCheckoutModal = closeCheckoutModal;
window.submitCheckout = submitCheckout;
window.processFilters = processFilters;
window.resetFilters = resetFilters;
window.updatePriceSlider = updatePriceSlider;
window.setSliderSlide = setSliderSlide;
window.startHeroSliderTimer = startHeroSliderTimer;