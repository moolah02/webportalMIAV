<x-guest-layout>
    <!-- Login Header -->
    <div class="login-header">
        <div class="company-logo">
            <img src="{{ asset('logo/revival logo.jpeg') }}" alt="Revival Technologies Logo" class="login-logo">
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
            <i class="fas fa-sign-in-alt"></i> Sign In
        </button>
    </form>

    <!-- Footer -->
    <div class="login-footer">
        <p>&copy; {{ date('Y') }} Revival Technologies. All rights reserved.</p>
        <p style="margin-block-start: 0.5rem; font-size: 0.8rem;">
            Secure authentication 
        </p>
    </div>

    <style>
        /* FontAwesome CDN for icons */
        @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

        /* Login Logo Styling */
        .company-logo {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1.5rem;
            /* Remove any background or styling that creates the blue box */
            background: none !important;
            background-color: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }

        /* Remove any pseudo-elements that might create background boxes */
        .company-logo::before,
        .company-logo::after {
            display: none !important;
            content: none !important;
        }

        .login-logo {
            height: 80px; /* Larger for login page */
            width: auto;
            max-width: 200px;
            object-fit: contain;
            transition: transform 0.3s ease;
            /* Completely remove any background */
            background: none !important;
            background-color: transparent !important;
            box-shadow: none !important;
            border: none !important;
            /* Use drop-shadow instead of box-shadow for transparency */
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.1));
        }

        .login-logo:hover {
            transform: scale(1.05);
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.15));
        }

        /* Alternative logo background fixes - uncomment if needed */
        
        /* If logo has white background that needs to be transparent */
        /*
        .login-logo {
            background: transparent;
            filter: brightness(0) saturate(100%) invert(25%) sepia(15%) saturate(1000%) hue-rotate(180deg);
        }
        */

        /* If you want to force remove any background */
        /*
        .login-logo {
            background: transparent !important;
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
        }
        */

        /* If logo needs to be made transparent */
        /*
        .login-logo {
            opacity: 0.9;
            background-color: transparent;
        }
        */

        /* Enhanced button styling */
        .login-button {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-weight: 600;
            font-size: 1rem;
            padding: 0.875rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .login-button i {
            font-size: 1rem;
        }

        /* Alternative sizing options - uncomment one if needed */

        /* Extra Large Logo */
        /*
        .login-logo {
            height: 100px;
            max-width: 250px;
        }
        */

        /* Medium Logo */
        /*
        .login-logo {
            height: 60px;
            max-width: 150px;
        }
        */

        /* Square Logo Styling (if your logo is square) */
        /*
        .login-logo {
            height: 80px;
            width: 80px;
            border-radius: 50%; /* Circular */
        }
        */

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-logo {
                height: 64px;
                max-width: 160px;
            }
        }

        @media (max-width: 480px) {
            .login-logo {
                height: 56px;
                max-width: 140px;
            }
        }

        /* Enhanced company name styling when logo is present */
        .company-name {
            margin-top: 0.5rem;
            font-size: 1.75rem;
            font-weight: 700;
            color: #1a202c;
            text-align: center;
        }

        /* Ensure login header is properly centered */
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-subtitle {
            margin-top: 0.75rem;
            color: #6b7280;
            font-size: 1rem;
        }
    </style>
</x-guest-layout>