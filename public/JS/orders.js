document.addEventListener('DOMContentLoaded', function() {
    
    const filterButtons = document.querySelectorAll('.filter-btn');
    const orderCards = document.querySelectorAll('.order-card');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // 1. Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            
            // 2. Add active class to clicked button
            this.classList.add('active');
            
            // 3. Get the filter value (all, pending, shipped, delivered)
            const filterValue = this.getAttribute('data-filter');

            // 4. Loop through cards and show/hide based on status
            orderCards.forEach(card => {
                const cardStatus = card.getAttribute('data-status');
                
                if (filterValue === 'all' || cardStatus === filterValue) {
                    card.style.display = 'flex';
                    // Optional: Add a small boot-up animation effect when shown
                    card.style.opacity = '0';
                    setTimeout(() => { card.style.opacity = '1'; }, 50);
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

});