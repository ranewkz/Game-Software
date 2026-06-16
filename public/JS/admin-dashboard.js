const ctx = document.getElementById('revenueChart');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: [
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'May',
            'Jun'
        ],
        datasets: [{
            label: 'Revenue',
            data: [
                1000,
                2200,
                1800,
                3500,
                4200,
                5100
            ],
            borderColor: '#00e5ff',
            tension: 0.4
        }]
    },
    options: {
        responsive: true
    }
});