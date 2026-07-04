/* ==========================================================================
   STORM // ADMIN CORE JAVASCRIPT
   ========================================================================= */

// --- TAB SWITCHING LOGIC ---
function switchTab(tabId) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
    
    // Remove active state from all sidebar items
    document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));
    
    // Show the targeted tab
    const targetTab = document.getElementById(tabId);
    if (targetTab) targetTab.classList.add('active');
    
    // Highlight the clicked sidebar item
    const activeNavItem = Array.from(document.querySelectorAll('.nav-item')).find(item => item.textContent.toLowerCase().includes(tabId.toLowerCase()));
    if(activeNavItem) activeNavItem.classList.add('active');
    
    // Save the active tab to browser memory
    localStorage.setItem('activeAdminTab', tabId);
}

// --- FILE UPLOAD DISPLAY UPDATE ---
function updateFileName(input) {
    const display = document.getElementById('file-name-display');
    if(input.files && input.files[0]) {
        display.textContent = "// FILE SELECTED: " + input.files[0].name.toUpperCase();
        display.style.color = "#00f0ff";
    } else {
        display.textContent = "CLICK TO BROWSE OR DRAG IMAGE HERE";
        display.style.color = "#00f0ff";
    }
}

// --- CRUD MODAL LOGIC ---
function openEditModal(id, title, price, image) {
    document.getElementById('editModal').classList.add('active');
    document.getElementById('editTitle').value = title;
    document.getElementById('editPrice').value = price;
    document.getElementById('editImage').value = image; 
    document.getElementById('editForm').action = `/admin/games/update/${id}`;
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
}

// --- ROLE MODAL LOGIC ---
function openRoleModal(currentType, id, name) {
    document.getElementById('roleModal').classList.add('active');
    document.getElementById('roleUserName').value = name;
    document.getElementById('roleSelect').value = currentType;
    document.getElementById('roleForm').action = `/admin/users/update-role/${currentType}/${id}`;
}

function closeRoleModal() {
    document.getElementById('roleModal').classList.remove('active');
}

// Close Modals on Background Click
window.onclick = function(event) {
    const editModal = document.getElementById('editModal');
    const roleModal = document.getElementById('roleModal');
    if (event.target == editModal) {
        closeEditModal();
    }
    if (event.target == roleModal) {
        closeRoleModal();
    }
}

// --- ON PAGE LOAD INITIALIZATION & EVENT LISTENERS ---
document.addEventListener('DOMContentLoaded', () => {

    // 1. --- TAB MEMORY RESTORE ---
    const savedTab = localStorage.getItem('activeAdminTab');
    if (savedTab) {
        switchTab(savedTab);
    }

    // 2. --- CHART.JS INITIALIZATION (NEON THEME) ---
    if (typeof Chart !== 'undefined') {
        Chart.defaults.color = '#64748b';
        Chart.defaults.font.family = "'Share Tech Mono', monospace";

        const salesCanvas = document.getElementById('salesChart');
        if (salesCanvas) {
            const ctxSales = salesCanvas.getContext('2d');
            new Chart(ctxSales, {
                type: 'line',
                data: {
                    labels: ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN'],
                    datasets: [{
                        label: 'CREDITS ($)',
                        data: [1500, 2300, 3400, 2900, 4100, 5200],
                        borderColor: '#00f0ff',
                        backgroundColor: 'rgba(0, 240, 255, 0.1)',
                        borderWidth: 2,
                        pointBackgroundColor: '#070B14',
                        pointBorderColor: '#00f0ff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { grid: { color: 'rgba(255, 255, 255, 0.05)' } },
                        x: { grid: { color: 'rgba(255, 255, 255, 0.05)' } }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        }

        const pieCanvas = document.getElementById('pieChart');
        if (pieCanvas) {
            const ctxPie = pieCanvas.getContext('2d');
            new Chart(ctxPie, {
                type: 'doughnut',
                data: {
                    labels: ['ACTION', 'RPG', 'STRATEGY', 'CO-OP'],
                    datasets: [{
                        data: [45, 30, 15, 10],
                        backgroundColor: ['#00f0ff', '#ff0055', '#7000ff', '#0D1321'],
                        borderColor: '#070B14',
                        borderWidth: 2,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        }
    }

    // 3. --- LIVE SEARCH FILTER FOR GAME VAULT ---
    const searchInput = document.getElementById('liveSearchVault');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const gameRows = document.querySelectorAll('#vault .cyber-table tbody tr');

            gameRows.forEach(row => {
                if (row.cells.length <= 1) return; 
                const titleElement = row.querySelector('td:nth-child(2) strong');
                if (titleElement) {
                    const titleText = titleElement.textContent.toLowerCase();
                    row.style.display = titleText.includes(searchTerm) ? '' : 'none';
                }
            });
        });
    }

    // 4. --- LIVE BANNER PREVIEW LOGIC ---
    const syncFields = [
        { inputId: 'inputTagline', previewId: 'previewTagline', defaultText: '// CAMPAIGN TAGLINE' },
        { inputId: 'inputTitle', previewId: 'previewTitle', defaultText: 'MAIN HEADLINE WILL APPEAR HERE' },
        { inputId: 'inputDesc', previewId: 'previewDesc', defaultText: 'Your campaign description will automatically populate in this sector as you type...' },
        { inputId: 'inputBadge', previewId: 'previewBadge', defaultText: 'BADGE' },
        { inputId: 'inputAction', previewId: 'previewBtn', defaultText: 'ACTION TEXT' }
    ];

    syncFields.forEach(field => {
        const inputElement = document.getElementById(field.inputId);
        const previewElement = document.getElementById(field.previewId);
        
        if (inputElement && previewElement) {
            inputElement.addEventListener('input', (e) => {
                // Update text content
                previewElement.textContent = e.target.value || field.defaultText;
                
                // Special handling for the optional badge display
                if(field.inputId === 'inputBadge') {
                    previewElement.style.display = e.target.value ? 'block' : 'none';
                }
            });
        }
    });

    // 5. --- IMAGE PREVIEW FOR BANNER UPLOAD ---
    const imageInput = document.getElementById('banner_image');
    const previewCard = document.getElementById('previewCard');

    if (imageInput && previewCard) {
        imageInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const objectUrl = URL.createObjectURL(this.files[0]);
                previewCard.style.backgroundImage = `linear-gradient(rgba(0,0,0,0.6), rgba(7,11,20,0.9)), url('${objectUrl}')`;
            }
        });
    }
});