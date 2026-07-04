<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STORM // CREATE GAME</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Rajdhani:wght@600;700&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
</head>
<body style="display: flex; flex-direction: column; min-height: 100vh; background-color: #04070D;">

    <nav class="top-nav">
        <div class="brand">STEAM // <span>ADMIN CORE</span></div>
        <div class="admin-profile">
            <div class="avatar-info" style="margin-right: 20px; text-align: right;">
                <span class="avatar-name">{{ Session::get('user_name') ?? 'SYS_ADMIN' }}</span>
                <span class="avatar-role">SYS_ADMIN</span>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn-pink" style="text-decoration: none;">RETURN TO DASHBOARD</a>
        </div>
    </nav>

    <main style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px;">
        <div class="form-panel" style="width: 100%; max-width: 800px; padding: 40px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h2 class="section-title" style="margin-bottom: 0;">// INITIALIZE NEW GAME</h2>
            </div>
            
            @if(session('success'))
                <div style="background: rgba(0, 240, 255, 0.1); border-left: 4px solid #00f0ff; color: #00f0ff; padding: 15px; margin-bottom: 25px; font-family: 'Share Tech Mono', monospace;">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('admin.games.store') }}" method="POST">
                @csrf
                <div class="profile-grid">
                    <div class="form-group full-span">
                        <label>TITLE DESIGNATION</label>
                        <input type="text" name="title" class="cyber-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label>GENRE</label>
                        <input type="text" name="genre" class="cyber-input" placeholder="e.g. Action, RPG" required>
                    </div>

                    <div class="form-group">
                        <label>PLATFORM</label>
                        <input type="text" name="platform" class="cyber-input" placeholder="e.g. PC, PS5" required>
                    </div>

                    <div class="form-group">
                        <label>CREDIT COST ($)</label>
                        <input type="number" step="0.01" name="price" class="cyber-input" required>
                    </div>

                    <div class="form-group">
                        <label>HOLOGRAPHIC COVER (FILE NAME)</label>
                        <input type="text" name="image" class="cyber-input" placeholder="e.g. filename.jpg" required>
                    </div>

                    <div class="form-group full-span" style="margin-top: 20px;">
                        <button type="submit" class="btn-cyan full-width" style="padding: 15px; font-size: 1.2rem;">UPLOAD TO VAULT</button>
                    </div>
                </div>
            </form>
        </div>
    </main>

</body>
</html>