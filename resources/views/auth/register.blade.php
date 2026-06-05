<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REGISTER // ACCESS PORTAL</title>
    <link rel="stylesheet" href="{{ asset('css/gamer-auth.css') }}">
</head>
<body>

    <div class="auth-container">
        <div class="auth-header">
            <h1>DEPLOY SYSTEM ACCOUNT</h1>
            <p>CREATE YOUR PLAYER PROFILE IDENTIFICATION</p>
        </div>

        @if($errors->any())
            <div class="alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('register.submit') }}" method="POST">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label for="name">FULL OPERATIONAL NAME</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autocomplete="name">
                </div>

                <div class="form-group">
                    <label for="email">SECURE EMAIL ADDRESS</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                </div>

                <div class="form-group">
                    <label for="phone">CONTACT TELEPHONY NUMBER</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required autocomplete="tel">
                </div>

                <div class="form-group">
                    <label for="gender">GENDER PROFILE IDENTITY</label>
                    <select id="gender" name="gender" required>
                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>MALE</option>
                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>FEMALE</option>
                        <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>OTHER</option>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label for="address">PHYSICAL CARGO ROUTING ADDRESS</label>
                    <input type="text" id="address" name="address" value="{{ old('address') }}" required autocomplete="street-address">
                </div>

                <div class="form-group full-width">
                    <label for="dob">DATE OF NATALITY (DOB)</label>
                    <input type="date" id="dob" name="dob" value="{{ old('dob') }}" required>
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