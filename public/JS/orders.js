document.addEventListener('DOMContentLoaded', function() {
    
    const filterButtons = document.querySelectorAll('.filter-btn');
    const orderCards = document.querySelectorAll('.order-card');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all
            filterButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked
            this.classList.add('active');
            
            const filterValue = this.getAttribute('data-filter');

            // Loop cards and show/hide
            orderCards.forEach(card => {
                const cardStatus = card.getAttribute('data-status');
                
                if (filterValue === 'all' || cardStatus === filterValue) {
                    card.style.display = 'flex';
                    card.style.opacity = '0';
                    setTimeout(() => { card.style.opacity = '1'; }, 50);
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

});

// --- RECEIPT MODAL LOGIC ---
function openOrderReceipt(btn) {
    const id = btn.getAttribute('data-id');
    const date = btn.getAttribute('data-date');
    const address = btn.getAttribute('data-address');
    const total = btn.getAttribute('data-total');
    let items = [];
    
    try {
        items = JSON.parse(btn.getAttribute('data-items'));
    } catch(e) {
        // Fallback safety for legacy orders created before the receipt system
        items = [{ title: "Legacy System Record", qty: 1, price: parseFloat(total), format: "digital" }];
    }
    
    // Failsafe applied here too just in case!
    document.getElementById('rec-id').innerText = id || ('STM-' + Math.floor(Math.random() * 90000 + 10000));
    document.getElementById('rec-date').innerText = date || new Date().toLocaleString();
    document.getElementById('rec-address').innerText = address;
    
    let itemsHtml = '';
    let sub = 0, fr = 0;
    
    items.forEach(item => {
        let base = parseFloat(item.price);
        let cost = item.format === 'physical' ? base + 12 : base;
        let qty = parseInt(item.qty);
        let line = cost * qty;
        sub += (base * qty);
        if(item.format === 'physical') fr += (12 * qty);
        
        itemsHtml += `<div class="r-row">
            <span class="r-item-title">${qty}x ${item.title}</span>
            <span class="r-item-price">$${line.toFixed(2)}</span>
        </div>`;
    });
    
    document.getElementById('rec-items').innerHTML = itemsHtml;
    document.getElementById('rec-sub').innerText = '$' + sub.toFixed(2);
    document.getElementById('rec-freight').innerText = '$' + fr.toFixed(2);
    document.getElementById('rec-total').innerText = '$' + (sub + fr).toFixed(2);
    
    document.getElementById('fullReceiptModal').style.display = 'flex';
}

function closeOrderReceipt() {
    document.getElementById('fullReceiptModal').style.display = 'none';
}