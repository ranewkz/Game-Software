<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN // ACCESS PORTAL</title>
    <link rel="stylesheet" href="{{ asset('css/gamer-auth.css') }}">
</head>
<body>

    <div class="auth-container">
        <div class="auth-header">
            <h1>ACCESS TERMINAL</h1>
            <p>ENTER CREDENTIALS TO LOG IN</p>
        </div>

        @if($errors->has('login_error'))
            <div class="alert-danger">
                {{ $errors->first('login_error') }}
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf

            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="email">USER EMAIL</label>
                    <input type="email" id="email" name="email" required autocomplete="email">
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