<x-guest-layout>
    <!-- Login Header -->
    <div class="login-header">
        <div class="company-logo">
            🚀
        </div>
        <h1 class="company-name">Revival Technologies</h1>
        <p class="login-subtitle">Welcome back! Please sign in to your account</p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="success-message">
            {{ session('status') }}
        </div>
    @endif

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="error-message">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <!-- Login Form -->
    <form method="POST" action="{{ route('login') }}" autocomplete="on">
        @csrf

        <!-- Email Address -->
        <div class="form-group">
            <label for="email" class="form-label">
                📧 Email Address
            </label>
            <input 
                id="email" 
                class="form-input" 
                type="email" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                autofocus 
                autocomplete="username"
                placeholder="Enter your email address" />
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password" class="form-label">
                🔒 Password
            </label>
            <div class="password-container">
                <input 
                    id="password" 
                    class="form-input"
                    type="password"
                    name="password"
                    required 
                    autocomplete="current-password"
                    placeholder="Enter your password" />
                <button 
                    type="button" 
                    class="password-toggle" 
                    onclick="togglePassword()"
                    title="Show/Hide Password">
                    👁️‍🗨️
                </button>
            </div>
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="remember-container">
            <div class="remember-checkbox">
                <input 
                    id="remember_me" 
                    type="checkbox" 
                    name="remember">
                <label for="remember_me" class="remember-label">
                    Remember me
                </label>
            </div>

            @if (Route::has('password.request'))
                <a class="forgot-link" href="{{ route('password.request') }}">
                    Forgot password?
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <button type="submit" class="login-button">
            🚀 Sign In
        </button>
    </form>

    <!-- Footer -->
    <div class="login-footer">
        <p>&copy; {{ date('Y') }} Revival Technologies. All rights reserved.</p>
        <p style="margin-block-start: 0.5rem; font-size: 0.8rem;">
            Secure authentication powered by Laravel
        </p>
    </div>
</x-guest-layout>