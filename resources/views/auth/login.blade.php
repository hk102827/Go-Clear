@extends('layouts.guest')
@section('title', 'Admin Login')
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
                    <h1>Admin Dashboard</h1>
                    <p>Manage your Go Clear transportation platform with powerful administrative tools. Monitor operations, manage drivers and customers, and keep your business running smoothly.</p>
                </div>

                <div class="vehicle-illustration">
                    <img src="{{ asset('photo/car.svg') }}" alt="Go Clear Vehicle">
                </div>
            </div>
        </div>

        <!-- Right Section - Login Form -->
        <div class="right-section">
            <div class="form-container">
                <h2 class="form-title">Admin Sign In</h2>
                <p class="form-subtitle">Enter your credentials to access the admin panel</p>

                @if ($errors->any())
                    <div class="alert alert-error">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-error">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.submit') }}">
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

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="password-wrapper">
                            <input 
                                id="password" 
                                type="password" 
                                name="password" 
                                class="form-input" 
                                placeholder="Enter your password" 
                                required
                            >
                            <button 
                                type="button" 
                                class="toggle-password" 
                                onclick="togglePassword('password')" 
                                aria-label="Show password"
                            >
                                👁️
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="remember-label">
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span>Remember me</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="forgot-link">Forgot Password?</a>
                    </div>

                    <button type="submit" class="submit-btn">Sign In</button>
                </form>

                {{-- <div class="form-footer">
                    <p>Need help? <a href="#">Contact Support</a></p>
                </div> --}}
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