<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN // ACCESS PORTAL</title>
    <link rel="stylesheet" href="{{ asset('css/gamer-auth.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&display=swap" rel="stylesheet">
</head>
<body>

    <div class="auth-container">
        <div class="auth-header">
            <h1>ACCESS TERMINAL</h1>
            <p>ENTER CREDENTIALS TO LOG IN</p>
        </div>

        @if($errors->any())
            <div class="alert-danger" style="background: rgba(255, 0, 85, 0.1); border: 1px solid #ff0055; color: #ff0055; padding: 15px; margin-bottom: 20px; font-family: 'Share Tech Mono', monospace; text-align: left;">
                <ul style="list-style: none; padding: 0; margin: 0;">
                    @foreach($errors->all() as $error)
                        <li>// SYSTEM ERROR: {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="alert-success" style="background: rgba(0, 240, 255, 0.1); border: 1px solid #00f0ff; color: #00f0ff; padding: 15px; margin-bottom: 20px; font-family: 'Share Tech Mono', monospace; text-align: left;">
                // SUCCESS: {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf

            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="email">USER EMAIL</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                </div>

                <div class="form-group full-width">
                    <label for="password">SECURE KEYCODE</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                </div>

                <div class="full-width">
                    <button type="submit" class="btn-submit">AUTHENTICATE</button>
                </div>
            </div>
        </form>

        <div class="auth-footer">
            NEW OPERATOR? <a href="{{ route('register') }}">CREATE PROFILE</a>
        </div>
    </div>

</body>
</html>