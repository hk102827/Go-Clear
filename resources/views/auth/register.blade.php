<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign Up - Go Clear</title>
    <link rel="stylesheet" href="{{ secure_asset('css/auth.css') }}">

</head>

<body>
    <div class="container">
        <div class="logo-section">
            <div class="logo-left">
                <img src="{{ asset('photo/car.svg') }}" class="logo" alt="Go Clear Logo">
            </div>

            <div class="logo-right">
                <div class="welcome-text">Welcome to</div>
                <div class="brand-name">Go <span class="brand-clear">Clear</span></div>
            </div>
        </div>


        <div class="role-selector">
            <div class="role-label">Are you a Driver or Customer?</div>
            <div class="role-buttons">
                <button class="role-btn active" id="customerBtn">Customer</button>
                <button class="role-btn" id="driverBtn">Driver</button>
            </div>
        </div>

        <div class="form-section">
            <h2 class="form-title">Create Your <span id="roleType">Customer</span> Account</h2>

            @if ($errors->any())
                <div class="alert alert-error">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <input type="hidden" name="role" id="selectedRole" value="customer">

                <div class="form-group">
                    <input type="text" class="form-input" name="name" placeholder="Full Name"
                        value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <input type="email" class="form-input" name="email" placeholder="Email"
                        value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <input type="tel" class="form-input" name="phone" placeholder="Mobile Number"
                        value="{{ old('phone') }}" required>
                </div>

                <div class="form-group">
                    <input type="text" class="form-input" name="address" placeholder="UK Address"
                        value="{{ old('address') }}">
                </div>

                <div class="form-group">
                    <input type="text" class="form-input" name="postcode" placeholder="Postcode"
                        value="{{ old('postcode') }}">
                </div>

                <div class="form-group">
                    <div class="password-wrapper">
                        <input type="password" class="form-input" name="password" id="password" placeholder="Password"
                            required>
                        <span class="toggle-password" onclick="togglePassword('password')">👁️</span>
                    </div>
                </div>

                <div class="form-group">
                    <div class="password-wrapper">
                        <input type="password" class="form-input" name="password_confirmation"
                            id="password_confirmation" placeholder="Confirm Password" required>
                        <span class="toggle-password" onclick="togglePassword('password_confirmation')">👁️</span>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Next</button>
            </form>

            <div class="switch-form">
                Already Have An Account? <a href="{{ route('login') }}">Sign In</a>
            </div>
        </div>
    </div>

    <script>
        const customerBtn = document.getElementById('customerBtn');
        const driverBtn = document.getElementById('driverBtn');
        const roleInput = document.getElementById('selectedRole');
        const roleType = document.getElementById('roleType');

        customerBtn.addEventListener('click', function() {
            customerBtn.classList.add('active');
            driverBtn.classList.remove('active');
            roleInput.value = 'customer';
            roleType.textContent = 'Customer';
        });

        driverBtn.addEventListener('click', function() {
            driverBtn.classList.add('active');
            customerBtn.classList.remove('active');
            roleInput.value = 'driver';
            roleType.textContent = 'Driver';
        });

        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>

</html>
