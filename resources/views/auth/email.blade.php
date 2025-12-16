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
                    <h1>Password Recovery</h1>
                    <p>Don't worry! It happens to the best of us. Enter your email address and we'll send you a link to reset your password.</p>
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
                <p class="form-subtitle">Enter your email address to receive a password reset link</p>

                @if (session('status'))
                    <div class="alert alert-success">
                        <span class="alert-icon">✓</span>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            class="form-input" 
                            placeholder="admin@goclear.com" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus
                        >
                    </div>

                    <button type="submit" class="submit-btn">
                        Send Reset Link
                    </button>
                </form>

                <div class="form-footer">
                    <p>Remember your password? <a href="{{ route('admin.login') }}">Sign In</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

@endsection