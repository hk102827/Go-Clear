{{-- resources/views/admin/auth/passwords/reset.blade.php --}}
@extends('layouts.guest')
@section('title', 'Reset Password')
@section('content')
<body>
    <div class="split-container">
        <!-- Left Section - Branding -->
        <div class="left-section">
            <div class="brand-content">
                <div class="logo-header">
                    <img src="{{ asset('photo/car.svg') }}" class="brand-logo" alt="Go Clear Logo">
                    <div class="brand-title">
                        <span class="brand-go">Go</span> 
                        <span class="brand-clear">Clear</span>
                    </div>
                </div>
                
                <div class="brand-description">
                    <h1>Password Reset</h1>
                    <p>Enter your new password below to secure your admin account. Make sure it’s strong and unique.</p>
                </div>

                <div class="security-illustration">
                    <div class="security-icon">🔒</div>
                    <p class="security-text">Your account security is our priority</p>
                </div>
            </div>
        </div>

        <!-- Right Section - Reset Form -->
        <div class="right-section">
            <div class="form-container">
                <div class="back-link-top">
                    <a href="{{ route('admin.login') }}" class="back-btn">
                        <span class="back-arrow">←</span> Back to Sign In
                    </a>
                </div>

                <h2 class="form-title">Reset Password</h2>
                <p class="form-subtitle">Enter your new password to access your admin account</p>

                @if ($errors->any())
                    <div class="alert alert-error">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            class="form-input" 
                            placeholder="admin@goclear.com" 
                            value="{{ $email ?? old('email') }}" 
                            required 
                            autofocus
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">New Password</label>
                        <div class="password-wrapper">
                            <input 
                                id="password" 
                                type="password" 
                                name="password" 
                                class="form-input" 
                                placeholder="Enter new password" 
                                required
                            >
                            <button type="button" class="toggle-password" onclick="togglePassword('password')" aria-label="Show password">👁️</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password-confirm">Confirm Password</label>
                        <div class="password-wrapper">
                            <input 
                                id="password-confirm" 
                                type="password" 
                                name="password_confirmation" 
                                class="form-input" 
                                placeholder="Confirm new password" 
                                required
                            >
                            <button type="button" class="toggle-password" onclick="togglePassword('password-confirm')" aria-label="Show password">👁️</button>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">Reset Password</button>
                </form>

                <div class="form-footer">
                    <p>Remember your password? <a href="{{ route('admin.login') }}">Sign In</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.type = el.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>
@endsection
