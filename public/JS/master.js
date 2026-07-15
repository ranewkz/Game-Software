document.addEventListener('DOMContentLoaded', () => {
    let cartData = JSON.parse(localStorage.getItem('steam_cart')) || [];
    let totalItems = 0;
    
    cartData.forEach(item => {
        totalItems += parseInt(item.qty);
    });
    
    const cartCountEl = document.getElementById('cartCount');
    if (cartCountEl) {
        cartCountEl.innerText = totalItems;
    }
    
    let wishlistData = JSON.parse(localStorage.getItem('steam_wishlist')) || [];
    const wishlistCountEl = document.getElementById('wishlistCount');
    if (wishlistCountEl) {
        wishlistCountEl.innerText = wishlistData.length;
    }
});