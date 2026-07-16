<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STORM // ADMIN CORE</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Rajdhani:wght@600;700&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .game-cover { width: 50px; height: 70px; object-fit: cover; border: 1px solid var(--neon-cyan); border-radius: 2px; background-color: #070b14; }
        .platform-badge { background: rgba(255, 255, 255, 0.1); padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; color: #fff; }
        .stock-green { background: #00cc66; color: #000; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; font-weight: bold; font-family: 'Share Tech Mono', monospace; }
        .stock-yellow { background: #ffb703; color: #000; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; font-weight: bold; font-family: 'Share Tech Mono', monospace; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body>

    <nav class="top-nav">
        <div class="brand">STEAM // <span>ADMIN CORE</span></div>
        <div class="admin-profile">
            <div class="avatar-container" onclick="switchTab('profile')">
                <img src="{{ Session::get('user_avatar') ? asset(Session::get('user_avatar')) : 'https://ui-avatars.com/api/?name='.urlencode(Session::get('user_name') ?? 'Admin').'&background=00f0ff&color=070B14' }}" alt="Profile" class="profile-avatar">
                <div class="avatar-info">
                    <span class="avatar-name">{{ Session::get('user_name') ?? 'SYS_ADMIN' }}</span>
                    <span class="avatar-role">SYS_ADMIN</span>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="GET" style="display:inline; margin-left: 20px;">
                <button type="submit" class="btn-pink">LOGOUT</button>
            </form>
        </div>
    </nav>

    <div class="dashboard-layout">
        <aside class="sidebar">
            <ul class="nav-menu">
                <li class="nav-item" onclick="switchTab('overview')">OVERVIEW</li>
                <li class="nav-item" onclick="switchTab('users')">USER MATRIX</li>
                <li class="nav-item" onclick="switchTab('vault')">GAME VAULT</li>
                <li class="nav-item" onclick="switchTab('campaigns')">CAMPAIGNS & BANNERS</li>
                <li class="nav-item" onclick="switchTab('promos')">PROMO MANAGEMENT</li>
            </ul>
        </aside>

        <main class="main-content">
            
            @if ($errors->any())
                <div style="background: rgba(255,0,85,0.1); border: 1px dashed var(--neon-pink); padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                    <strong style="color: var(--neon-pink); font-family: var(--font-heading);">// SYSTEM REJECTION:</strong>
                    <ul style="margin-top: 10px; margin-left: 20px; color: #fff; font-size: 0.9rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div style="background: rgba(0,240,255,0.1); border: 1px solid var(--neon-cyan); padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                    <strong style="color: var(--neon-cyan); font-family: var(--font-heading);">// SYSTEM UPDATE:</strong>
                    <span style="color: #fff; font-size: 0.9rem;">{{ session('success') }}</span>
                </div>
            @endif

            <div id="overview" class="tab-content">
                <h2 class="section-title">// SYSTEM OVERVIEW: LIVE METRICS</h2>
                <div class="stats-grid">
                    <div class="stat-card"><div class="stat-label">TOTAL OPERATIVES</div><div class="stat-value">{{ isset($users) ? count($users) : 0 }}</div></div>
                    <div class="stat-card"><div class="stat-label">GAMES IN VAULT</div><div class="stat-value">{{ isset($games) ? count($games) : 0 }}</div></div>
                    <div class="stat-card"><div class="stat-label">CREDITS ACQUIRED</div><div class="stat-value">$12,450</div></div>
                </div>

                <div class="charts-grid" style="margin-top: 30px;">
                    <div class="chart-container">
                        <h3 class="chart-title">// REVENUE TIMELINE</h3>
                        <canvas id="salesChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h3 class="chart-title">// SALES BY GENRE</h3>
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
                
                <div style="margin-top: 40px;">
                    <h2 class="section-title">// RECENT OPERATIVE MATRIX</h2>
                    <div class="data-table-wrapper">
                        <table class="cyber-table" style="pointer-events: none;">
                            <thead><tr><th>ID</th><th>OPERATIVE NAME</th><th>COMM-LINK</th><th>ROLE</th></tr></thead>
                            <tbody>
                                @if(isset($users) && count($users) > 0)
                                    @foreach($users->take(5) as $user)
                                    <tr>
                                        <td>#{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if($user->role === 'admin' || $user->role_id == 1) <span class="status-pink">ADMIN</span>
                                            @else <span class="status-cyan">USER</span> @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="4" style="text-align:center;">NO OPERATIVES FOUND.</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="users" class="tab-content">
                <h2 class="section-title">// USER MATRIX: ROLE MANAGEMENT</h2>
                <div class="data-table-wrapper" style="margin-bottom: 40px;">
                    <table class="cyber-table">
                        <thead><tr><th>ID</th><th>OPERATIVE NAME</th><th>COMM-LINK</th><th>STATUS</th><th>ACTIONS</th></tr></thead>
                        <tbody>
                            @if(isset($users) && count($users) > 0)
                                @foreach($users as $user)
                                <tr>
                                    <td>#{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->role === 'admin' || $user->role_id == 1) <span class="status-pink">ADMIN</span>
                                        @else <span class="status-cyan">USER</span> @endif
                                    </td>
                                    <td class="action-cell">
                                        <div style="display: flex; gap: 8px; align-items: stretch; height: 32px;">
                                            <button class="btn-cyan" style="margin: 0; display: flex; align-items: center; justify-content: center; padding: 0 15px; font-family: 'Share Tech Mono', monospace; font-weight: bold; font-size: 0.85rem;" onclick="openRoleModal('{{ $user->role ?? 'user' }}', {{ $user->id }}, '{{ addslashes($user->name) }}')">EDIT ROLE</button>
                                            
                                            <form action="{{ route('admin.users.ban', [$user->role ?? 'user', $user->id]) }}" method="POST" style="margin: 0; display: flex;">
                                                @csrf
                                                <button type="submit" class="btn-ban" style="margin: 0; border-color: {{ isset($user->status) && $user->status === 'banned' ? '#00f0ff' : '#ff0055' }}; color: {{ isset($user->status) && $user->status === 'banned' ? '#00f0ff' : '#ff0055' }}; background: transparent; display: flex; align-items: center; justify-content: center; padding: 0 15px; cursor: pointer; font-family: 'Share Tech Mono', monospace; font-weight: bold; border-width: 1px; border-style: solid; font-size: 0.85rem;">
                                                    {{ isset($user->status) && $user->status === 'banned' ? 'UNBAN' : 'BAN' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr><td colspan="5" style="text-align:center;">NO OPERATIVES FOUND.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="vault" class="tab-content">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 class="section-title" style="margin-bottom: 0;">// GAME VAULT: INVENTORY CONTROL</h2>
                </div>
                
                <div class="filter-bar" style="display: flex; align-items: center;">
                    <input type="text" id="liveSearchVault" class="cyber-input" style="margin-bottom: 0; max-width: 500px;" placeholder="Filter vault by title in real-time...">
                    <a href="{{ route('admin.games.create') }}" class="btn-cyan" style="margin-left: auto; text-decoration: none; padding: 10px 20px; font-weight: bold; display: flex; align-items: center; justify-content: center;">+ CREATE GAME</a>
                </div>

                <div class="data-table-wrapper">
                    <table class="cyber-table" id="vaultTable">
                        <thead>
                            <tr>
                                <th>COVER</th>
                                <th>TITLE & GENRE</th>
                                <th>PLATFORM</th>
                                <th>PRICE</th>
                                <th>STOCK LEVEL</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($games) && count($games) > 0)
                                @foreach($games as $game)
                                    <tr>
                                        <td>
                                            <img src="{{ asset('assets/images/' . $game->image) }}" class="game-cover" alt="Cover" onerror="this.onerror=null; this.src='https://placehold.co/50x70/070b14/00f0ff?text=NO+IMG';">
                                        </td>
                                        <td>
                                            <strong class="game-title-text" style="font-size: 1.05rem;">{{ $game->title }}</strong><br>
                                            <span style="font-size: 0.8rem; color: var(--text-muted);">{{ $game->genre ?? 'General' }}</span>
                                        </td>
                                        <td><span class="platform-badge">{{ $game->platform ?? 'PC' }}</span></td>
                                        <td style="color: var(--neon-cyan); font-weight: bold;">${{ number_format($game->price, 2) }}</td>
                                        <td>
                                            @php $stock = $game->stock ?? rand(0, 30); @endphp
                                            @if($stock <= 0)
                                                <span class="badge-pink">OUT OF STOCK</span>
                                            @elseif($stock < 10)
                                                <span class="stock-yellow">LOW: {{ $stock }} LEFT</span>
                                            @else
                                                <span class="stock-green">{{ $stock }} IN STOCK</span>
                                            @endif
                                        </td>
                                        <td class="action-cell">
                                            <div style="display: flex; gap: 8px; align-items: stretch; height: 32px;">
                                                <button type="button" class="btn-cyan" style="margin: 0; display: flex; align-items: center; justify-content: center; padding: 0 15px; font-family: 'Share Tech Mono', monospace; font-weight: bold; font-size: 0.85rem;" onclick="openEditModal({{ $game->id }}, '{{ addslashes($game->title) }}', {{ $game->price }}, '{{ addslashes($game->image) }}')">EDIT</button>
                                                
                                                <form action="{{ route('admin.games.destroy', $game->id) }}" method="POST" style="margin: 0; display: flex;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn-pink" style="margin: 0; display: flex; align-items: center; justify-content: center; padding: 0 15px; font-family: 'Share Tech Mono', monospace; font-weight: bold; font-size: 0.85rem;" onclick="return confirm('Purge {{ $game->title }}?');">PURGE</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr><td colspan="6" style="text-align:center; padding: 20px;">NO DATA FOUND IN VAULT.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="campaigns" class="tab-content">
                <h2 class="section-title">// DEPLOY NEW CAMPAIGN BANNER</h2>
                
                <div class="preview-split-layout" style="margin-bottom: 50px;">
                    
                    <div class="form-panel cyber-panel-glow">
                        <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label>BANNER BACKGROUND IMAGE (UPLOAD)</label>
                                <div class="cyber-file-dropzone">
                                    <input type="file" name="banner_image" id="banner_image" class="file-input-hidden" accept="image/*" required onchange="updateFileName(this)">
                                    <label for="banner_image" class="file-upload-content">
                                        <svg class="upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        <span id="file-name-display" class="upload-text">CLICK TO BROWSE OR DRAG IMAGE HERE</span>
                                        <span class="upload-subtext">Supports JPG, PNG, WEBP (Max 5MB)</span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>CAMPAIGN TAGLINE</label>
                                <input type="text" id="inputTagline" name="campaign_tag" class="cyber-input" placeholder="e.g. // WINTER EXCLUSIVE" required>
                            </div>
                            <div class="form-group">
                                <label>MAIN HEADLINE</label>
                                <input type="text" id="inputTitle" name="title" class="cyber-input" placeholder="e.g. HOLIDAY PRICE CRASH" required>
                            </div>
                            <div class="form-group">
                                <label>CAMPAIGN DESCRIPTION</label>
                                <input type="text" id="inputDesc" name="description" class="cyber-input" placeholder="Enter brief details..." required>
                            </div>
                            <div class="split-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="form-group">
                                    <label>BADGE TEXT (OPTIONAL)</label>
                                    <input type="text" id="inputBadge" name="badge_text" class="cyber-input" placeholder="e.g. FLAT $12 SAVER">
                                </div>
                                <div class="form-group">
                                    <label>BUTTON ACTION TEXT</label>
                                    <input type="text" id="inputAction" name="button_text" class="cyber-input" placeholder="e.g. DISCOVER NOW" required>
                                </div>
                            </div>
                            <div class="form-group" style="margin-top: 25px;">
                                <button type="submit" class="btn-cyan full-width" style="padding: 18px; font-size: 1.1rem; font-weight: bold; letter-spacing: 2px;">AUTHORIZE & DEPLOY BANNER</button>
                            </div>
                        </form>
                    </div>

                    <div class="live-preview-panel">
                        <h3 class="preview-label">// LIVE STOREFRONT PREVIEW</h3>
                        <div class="preview-banner-card" id="previewCard" style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(7,11,20,0.9)), url('https://placehold.co/600x300/070b14/00f0ff?text=UPLOAD+IMAGE');">
                            <div class="preview-content">
                                <span class="preview-tag" id="previewTagline">// CAMPAIGN TAGLINE</span>
                                <h2 class="preview-title" id="previewTitle">MAIN HEADLINE WILL APPEAR HERE</h2>
                                <p class="preview-desc" id="previewDesc">Your campaign description will automatically populate in this sector as you type...</p>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <span class="preview-badge" id="previewBadge" style="display: none;">BADGE</span>
                                    <button class="btn-cyan preview-btn" id="previewBtn">ACTION TEXT</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <h2 class="section-title">// ACTIVE BANNER ROTATION</h2>
                <div class="banner-grid">
                    @if(isset($banners) && count($banners) > 0)
                        @foreach($banners as $banner)
                        <div class="banner-card">
                            <img src="{{ asset('assets/images/banners/' . $banner->image) }}" alt="Banner Image">
                            <div class="banner-controls">
                                <div class="banner-meta-info">
                                    <strong class="banner-card-title">{{ $banner->title }}</strong>
                                    <span class="banner-card-tag">{{ $banner->campaign_tag }}</span>
                                </div>
                                <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" class="btn-pink" onclick="return confirm('Purge this banner from the storefront?');">PURGE FROM SYSTEM</button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="cyber-empty-state">
                            <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <h4 class="empty-state-title">NO ACTIVE CAMPAIGNS DETECTED</h4>
                            <p class="empty-state-desc">The storefront slider is currently inactive. Upload and deploy a banner above to populate the feed.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div id="promos" class="tab-content">
                <h2 class="section-title">// PROMOTIONAL MATRIX: SYSTEM DISCOUNTS</h2>
                
                <div class="preview-split-layout" style="margin-bottom: 50px;">
                    <div class="form-panel cyber-panel-glow">
                        <form action="{{ route('admin.promos.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>PROMO CODE STRING</label>
                                <input type="text" name="code" class="cyber-input" placeholder="e.g. CYBER26" required style="text-transform: uppercase;">
                            </div>
                            <div class="form-group">
                                <label>DISCOUNT VALUE ($)</label>
                                <input type="number" step="0.01" name="discount_amount" class="cyber-input" placeholder="e.g. 25.00" required>
                            </div>
                            <div class="split-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="form-group">
                                    <label>MAXIMUM ALLOWED USES</label>
                                    <input type="number" name="max_uses" class="cyber-input" value="100" required>
                                </div>
                                <div class="form-group">
                                    <label>EXPIRATION DATE</label>
                                    <input type="date" name="expires_at" class="cyber-input">
                                </div>
                            </div>
                            <div class="form-group" style="margin-top: 25px;">
                                <button type="submit" class="btn-cyan full-width" style="padding: 18px; font-size: 1.1rem; font-weight: bold; letter-spacing: 2px;">DEPLOY PROMO OVERRIDE</button>
                            </div>
                        </form>
                    </div>

                    <div class="live-preview-panel">
                        <h3 class="preview-label">// ACTIVE DISCOUNT OVERRIDES</h3>
                        <div class="data-table-wrapper" style="max-height: 400px; overflow-y: auto;">
                            <table class="cyber-table">
                                <thead>
                                    <tr>
                                        <th>CODE</th>
                                        <th>VALUE</th>
                                        <th>USAGE</th>
                                        <th>STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($promos) && count($promos) > 0)
                                        @foreach($promos as $promo)
                                            <tr>
                                                <td><strong style="color: var(--neon-cyan);">{{ $promo->code }}</strong></td>
                                                <td>${{ number_format($promo->discount_amount, 2) }}</td>
                                                <td>{{ $promo->uses_count }} / {{ $promo->max_uses }}</td>
                                                <td>
                                                    @if($promo->expires_at && \Carbon\Carbon::parse($promo->expires_at)->isPast())
                                                        <span class="badge-pink">EXPIRED</span>
                                                    @elseif($promo->uses_count >= $promo->max_uses)
                                                        <span class="stock-yellow">DEPLETED</span>
                                                    @else
                                                        <span class="stock-green">ACTIVE</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr><td colspan="4" style="text-align:center;">NO PROMO CODES ACTIVATED.</td></tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div id="profile" class="tab-content">
                <h2 class="section-title">// SYSTEM ADMIN: PROFILE OVERRIDE</h2>
                <div class="form-panel" style="max-width: 800px;">
                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="profile-grid">
                            <div class="form-group full-span">
                                <label>HOLOGRAPHIC AVATAR (UPLOAD IMAGE)</label>
                                <input type="file" name="avatar" class="cyber-input file-upload-wrapper" accept="image/*">
                            </div>
                            <div class="form-group">
                                <label>OPERATIVE NAME</label>
                                <input type="text" name="name" class="cyber-input" value="{{ Session::get('user_name') }}" required>
                            </div>
                            <div class="form-group">
                                <label>COMM-LINK (EMAIL)</label>
                                <input type="email" name="email" class="cyber-input" value="{{ Session::get('user_email') }}" required>
                            </div>
                            <div class="form-group full-span" style="margin-top: 15px;">
                                <button type="submit" class="btn-cyan full-width" style="padding: 15px; font-size: 1.1rem; font-weight: bold;">EXECUTE FULL OVERRIDE</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </main>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3 class="section-title" style="margin-bottom: 20px;">// OVERRIDE GAME DATA</h3>
            <form id="editForm" method="POST">
                @csrf
                <div class="form-group">
                    <label>TITLE DESIGNATION</label>
                    <input type="text" id="editTitle" name="title" class="cyber-input" required>
                </div>
                <div class="form-group">
                    <label>CREDIT COST ($)</label>
                    <input type="number" step="0.01" id="editPrice" name="price" class="cyber-input" required>
                </div>
                <div class="form-group">
                    <label>HOLOGRAPHIC COVER (FILE NAME)</label>
                    <input type="text" id="editImage" name="image" class="cyber-input" required>
                </div>
                <div class="action-cell" style="margin-top: 25px; display: flex; gap: 10px;">
                    <button type="submit" class="btn-cyan" style="flex: 1; margin: 0; font-weight: bold;">SAVE CHANGES</button>
                    <button type="button" class="btn-pink" style="flex: 1; margin: 0; font-weight: bold;" onclick="closeEditModal()">CANCEL</button>
                </div>
            </form>
        </div>
    </div>

    <div id="roleModal" class="modal">
        <div class="modal-content">
            <h3 class="section-title" style="margin-bottom: 20px;">// REASSIGN CLEARANCE LEVEL</h3>
            <form id="roleForm" method="POST">
                @csrf
                <div class="form-group">
                    <label>OPERATIVE NAME</label>
                    <input type="text" id="roleUserName" class="cyber-input" disabled style="opacity: 0.7;">
                </div>
                <div class="form-group">
                    <label>NEW CLEARANCE (ROLE)</label>
                    <select id="roleSelect" name="new_role" class="cyber-input" required style="background: #070B14; color: #fff;">
                        <option value="admin">ADMINISTRATOR</option>
                        <option value="user">STANDARD USER</option>
                    </select>
                </div>
                <div class="action-cell" style="margin-top: 25px; display: flex; gap: 10px;">
                    <button type="submit" class="btn-cyan" style="flex: 1; margin: 0; font-weight: bold;">OVERRIDE ROLE</button>
                    <button type="button" class="btn-pink" style="flex: 1; margin: 0; font-weight: bold;" onclick="closeRoleModal()">CANCEL</button>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('JS/admin-dashboard.js') }}"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const savedTab = localStorage.getItem('activeAdminTab') || 'overview';
            if (typeof switchTab === "function") {
                switchTab(savedTab);
            } else {
                document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
                const activeElement = document.getElementById(savedTab);
                if(activeElement) activeElement.classList.add('active');
            }
        });
    </script>
</body>
</html>