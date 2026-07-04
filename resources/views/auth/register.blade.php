<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REGISTER // DEPLOY PROFILE</title>
    <link rel="stylesheet" href="{{ asset('css/gamer-auth.css') }}">
</head>
<body>

    <div class="auth-container">
        <div class="auth-header">
            <h1>DEPLOY SYSTEM ACCOUNT</h1>
            <p>CREATE YOUR SYSTEM ACCESS IDENTIFICATION</p>
        </div>

        @if($errors->any())
            <div class="alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('register.submit') }}" method="POST">
            @csrf

            <div class="form-grid">
                <!-- THIS IS THE FIELD THAT WAS MISSING -->
                <div class="form-group full-width">
                    <label for="role_type">SELECT SYSTEM PROFILE ROLE</label>
                    <select id="role_type" name="role_type" required>
                        <option value="customer">CUSTOMER ACCESS (STASH & ORDER MANAGER)</option>
                        <option value="staff">STAFF RECRUIT (ENTERPRISE ADMIN MANAGEMENT)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="name">FULL OPERATIONAL NAME</label>
                    <input type="text" id="name" name="name" required autocomplete="name">
                </div>

                <div class="form-group">
                    <label for="email">SECURE EMAIL ADDRESS</label>
                    <input type="email" id="email" name="email" required autocomplete="email">
                </div>

                <div class="form-group">
                    <label for="phone">CONTACT TELEPHONY NUMBER</label>
                    <input type="text" id="phone" name="phone" required autocomplete="tel">
                </div>

                <div class="form-group">
                    <label for="gender">GENDER PROFILE IDENTITY</label>
                    <select id="gender" name="gender" required>
                        <option value="Male">MALE</option>
                        <option value="Female">FEMALE</option>
                        <option value="Other">OTHER</option>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label for="address">PHYSICAL CARGO ROUTING ADDRESS</label>
                    <input type="text" id="address" name="address" required autocomplete="street-address">
                </div>

                <div class="form-group full-width">
                    <label for="dob">DATE OF NATALITY (DOB)</label>
                    <input type="date" id="dob" name="dob" required>
                </div>

                <div class="form-group">
                    <label for="password">CREATE SYSTEM ACCESS PASSWORD</label>
                    <input type="password" id="password" name="password" required autocomplete="new-password">
                </div>

                <div class="form-group">
                    <label for="password_confirmation">RE-VERIFY ACCESS PASSWORD</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                </div>

                <div class="full-width">
                    <button type="submit" class="btn-submit">DEPLOY PROTOCOL</button>
                </div>
            </div>
        </form>

        <div class="auth-footer">
            EXISTING AGENT? <a href="{{ route('login') }}">SIGN IN SECURELY</a>
        </div>
    </div>

</body>
</html>