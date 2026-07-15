const stripe = Stripe(window.stripeKey);
const elements = stripe.elements();

const card = elements.create('card', {
    style: {
        base: {
            color: '#00f0ff',
            fontFamily: '"Rajdhani", sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': { color: '#8090a6' }
        },
        invalid: { color: '#ff003c', iconColor: '#ff003c' }
    }
});

card.mount('#card-element');
card.on('change', function(event) {
    const displayError = document.getElementById('card-errors');
    displayError.textContent = event.error ? event.error.message : '';
});

let cartData = JSON.parse(localStorage.getItem('steam_cart')) || [];
let cyberMap = null;
let cyberMarker = null;

document.addEventListener('DOMContentLoaded', () => {
    let totalItems = 0;
    cartData.forEach(item => totalItems += parseInt(item.qty));
    const cartCountEl = document.getElementById('cartCount');
    if(cartCountEl) cartCountEl.innerText = totalItems;
    
    let wishlistData = JSON.parse(localStorage.getItem('steam_wishlist')) || [];
    const wishlistCountEl = document.getElementById('wishlistCount');
    if(wishlistCountEl) wishlistCountEl.innerText = wishlistData.length;

    if(cartData.length === 0) {
        document.getElementById('checkoutLayout').style.display = 'none';
        document.getElementById('emptyStateLayout').style.display = 'block';
        return;
    }
    renderCheckoutItems();
});

function renderCheckoutItems() {
    const list = document.getElementById('checkoutItemsList');
    let subtotal = 0; let freight = 0; let html = '';

    cartData.forEach(item => {
        const itemCost = item.format === 'physical' ? parseFloat(item.price) + 12 : parseFloat(item.price);
        const lineTotal = itemCost * item.qty;
        subtotal += (parseFloat(item.price) * item.qty);
        if (item.format === 'physical') freight += (12 * item.qty);

        html += `
        <div class="checkout-item">
            <div class="item-left">
                <img src="${item.image || 'https://via.placeholder.com/60x80/020a14/00f0ff?text=NO+IMG'}" class="checkout-item-img">
                <div class="checkout-item-details">
                    <span class="checkout-item-title">${item.title}</span>
                    <span class="checkout-item-format">${item.format === 'physical' ? 'PHYSICAL CD' : 'DIGITAL LICENSE'} (x${item.qty})</span>
                </div>
            </div>
            <div class="checkout-item-price-block">
                <span class="unit-price">UNIT: $${itemCost.toFixed(2)}</span>
                <span class="checkout-item-price">$${lineTotal.toFixed(2)}</span>
            </div>
        </div>`;
    });

    list.innerHTML = html;
    const grandTotal = subtotal + freight;
    document.getElementById('summarySubtotal').innerText = '$' + subtotal.toFixed(2);
    document.getElementById('summaryFreight').innerText = '$' + freight.toFixed(2);
    document.getElementById('summaryTotal').innerText = '$' + grandTotal.toFixed(2);
}

function toggleNewAddressInput(value) {
    const group = document.getElementById('newAddressGroup');
    if (value === 'new') {
        group.style.display = 'block';
        
        if (!cyberMap) {
            cyberMap = L.map('cyberMap').setView([16.8409, 96.1735], 13);
            
            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                maxZoom: 19
            }).addTo(cyberMap);

            cyberMarker = L.marker([16.8409, 96.1735]).addTo(cyberMap);

            cyberMap.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;
                
                cyberMarker.setLatLng(e.latlng);
                document.getElementById('newAddressInput').value = "Uplinking coordinates...";

                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                    .then(res => res.json())
                    .then(data => {
                        const address = data.display_name || `Lat: ${lat.toFixed(4)}, Lng: ${lng.toFixed(4)}`;
                        document.getElementById('newAddressInput').value = address;
                    })
                    .catch(() => {
                        document.getElementById('newAddressInput').value = `Lat: ${lat.toFixed(4)}, Lng: ${lng.toFixed(4)}`;
                    });
            });
        }
        
        setTimeout(() => { 
            cyberMap.invalidateSize(); 
        }, 300);
        
    } else {
        group.style.display = 'none';
    }
}

document.getElementById('addressSelect').addEventListener('change', function(e) {
    toggleNewAddressInput(e.target.value);
});

document.getElementById('processCheckoutForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('btnSubmitCheckout');
    btn.disabled = true;
    btn.innerText = "ENCRYPTING STRIPE TOKEN...";

    const addressSelect = document.getElementById('addressSelect').value;
    const finalAddress = addressSelect === 'new' ? document.getElementById('newAddressInput').value : addressSelect;

    if (addressSelect === 'new' && (finalAddress.trim() === '' || finalAddress === 'Click on the map to set delivery zone...')) {
        alert("PROVIDE A VALID ROUTING ADDRESS.");
        btn.disabled = false;
        btn.innerText = "AUTHORIZE TRANSACTION";
        return;
    }

    const {token, error} = await stripe.createToken(card);

    if (error) {
        document.getElementById('card-errors').textContent = error.message;
        btn.disabled = false;
        btn.innerText = "AUTHORIZE TRANSACTION";
        return;
    }

    btn.innerText = "AUTHORIZING PAYMENT...";

    try {
        const response = await fetch(window.checkoutProcessUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify({ 
                cart: cartData, 
                address: finalAddress,
                stripeToken: token.id 
            })
        });

        const data = await response.json();

        if (response.ok && data.success) {
            localStorage.removeItem('steam_cart');
            btn.style.borderColor = "#00ff00";
            btn.style.color = "#00ff00";
            btn.innerText = "TRANSACTION APPROVED!";
            buildAndShowReceipt(data.items, finalAddress, data.order_id, data.date, cartData);
        } else {
            alert('TRANSACTION FAILED: ' + (data.error || 'System Error'));
            btn.disabled = false;
            btn.innerText = "AUTHORIZE TRANSACTION";
        }
    } catch (error) {
        alert('NETWORK ERROR: Could not reach the server.');
        btn.disabled = false;
        btn.innerText = "AUTHORIZE TRANSACTION";
    }
});

function buildAndShowReceipt(serverItems, address, orderId, date, localCart) {
    document.getElementById('rec-id').innerText = orderId;
    document.getElementById('rec-date').innerText = date;
    document.getElementById('rec-address').innerText = address;
    
    let keysHtml = '';
    serverItems.forEach(item => {
        keysHtml += `<div class="key-box">
            <span class="key-title">${item.qty}X ${item.title}</span>
            <span class="key-value">${item.key}</span>
        </div>`;
    });
    document.getElementById('rec-keys').innerHTML = keysHtml;
    
    let sub = 0, fr = 0;
    localCart.forEach(item => {
        sub += (parseFloat(item.price) * item.qty);
        if(item.format === 'physical') fr += (12 * item.qty);
    });
    
    document.getElementById('rec-sub').innerText = '$' + sub.toFixed(2);
    document.getElementById('rec-freight').innerText = '$' + fr.toFixed(2);
    document.getElementById('rec-total').innerText = '$' + (sub + fr).toFixed(2);
    
    document.getElementById('fullReceiptModal').style.display = 'flex';
}

document.getElementById('vaultRedirectBtn').addEventListener('click', function() {
    window.location.href = window.myOrdersUrl;
});