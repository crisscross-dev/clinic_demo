<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DEMO - Login</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo2_pdf.png') }}">
    @php
    function asset_versioned($path) {
    $file = public_path($path);
    if (file_exists($file)) {
    return asset($path) . '?v=' . filemtime($file);
    }
    return asset($path);
    }
    @endphp
    <style>
        body {
            background: url('{{ asset("images/background.jpg") }}') no-repeat center center;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/css/login.css'])
</head>

<body>
    <div class="login-container">
        <!-- Left Side - Branding -->
        <div class="login-left">
            <!-- Default Login Branding -->
            <div class="branding-content">
                <div class="clinic-logo">
                    <img src="{{ asset('images/logo2_pdf.png') }}" alt="Key Icon" width="80" height="70">
                </div>
                <h1>For clinic Demonstration</h1>
                <p>Professional Healthcare Management System</p>
                <p>Secure access to patient records, appointments, and clinic administration.</p>
            </div>
            <!-- Forgot Password Branding -->
            <div class="forgot-branding">
                <div class="clinic-logo">
                    <img src="{{ asset('icon/key.svg') }}" alt="Key Icon" width="40" height="40">
                </div>

                <h1>Reset Password</h1>
                <p>Account Recovery</p>
                <p>Enter your username and we'll send you a link to reset your password.</p>
            </div>
        </div>

        <!-- Right Side - Forms -->
        <div class="login-right">
            <div class="form-container">
                <!-- Login Form -->
                <form class="login-form" method="POST" action="{{ route('unified.login') }}">
                    @csrf
                    <h2>Welcome Clinic Demo</h2>
                    <p class="login-subtitle">Demo credentials are prefilled for testing purposes.</p>


                    @if ($errors->any())
                    <div class="alert alert-danger" id="login-error-alert" style="display: flex; flex-direction: column; gap: 0.3rem;">
                        @foreach ($errors->all() as $error)
                        <div style="display: flex; align-items: center;">
                            <i class="bi bi-exclamation-triangle-fill" style="color: #b71c1c; margin-right: 0.5rem;"></i>
                            <span>{{ $error }}</span>
                        </div>
                        @endforeach
                    </div>

                    <script>
                        // Auto-fade the alert after 5 seconds
                        setTimeout(function() {
                            var alert = document.getElementById('login-error-alert');
                            if (alert) {
                                alert.style.transition = 'opacity 0.5s ease';
                                alert.style.opacity = 0;
                                setTimeout(function() {
                                    alert.remove();
                                }, 600);
                            }
                        }, 5000);
                    </script>
                    @endif


                    <div class="form-group">
                        <div class="input-wrapper">
                            <input
                                type="text"
                                id="login_identifier"
                                name="login_identifier"
                                class="form-control"
                                value="{{ old('login_identifier', 'Demo@gmail.com') }}"
                                required
                                autocomplete="username"
                                autofocus>
                            <i class="bi bi-person-fill input-icon"></i>
                        </div>
                        <label for="login_identifier">Username or Email</label>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper password-field-wrapper">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control"
                                value="Password@123"
                                required
                                autocomplete="current-password">
                            <i class="bi bi-lock-fill input-icon"></i>
                            <button
                                type="button"
                                class="password-toggle"
                                id="togglePassword"
                                aria-label="Toggle password visibility">
                                <i class="bi bi-eye-fill"></i>
                            </button>
                        </div>
                        <label for="password">Password</label>
                    </div>


                    <!-- <div class="checkbox-wrapper">
                        <label class="custom-checkbox">
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span class="checkmark"></span>
                        </label>
                        <span style="color: #6b7280; font-weight: 500;">Remember me</span>
                    </div> -->

                    <button type="submit" class="btn-login">
                        <i class="bi bi-box-arrow-in-right" style="margin-right: 0.5rem;"></i>
                        Sign In
                    </button>

                    <div class="forgot-password">
                        <a href="#" id="forgot-password-link">
                            <i class="bi bi-key" style="margin-right: 0.25rem; font-size: 0.8rem;"></i>
                            Forgot your password?
                        </a>
                    </div>
                    <div class="forgot-password" style="margin-top: 1rem; text-align: center;">
                        <a href="{{ route('student.register') }}">
                            <i class="bi bi-person-plus" style="margin-right: 0.25rem; font-size: 0.8rem;"></i>
                            New student? Create an account
                        </a>
                    </div>

                    <!-- <div class="login-link" style="margin-top: 1rem; text-align: center;">
                        <a href="{{ route('student.register') }}" style="color: #1e5799; text-decoration: none; font-size: 0.95rem; font-weight: 500;">
                            <i class="bi bi-person-plus" style="margin-right: 0.25rem; font-size: 0.8rem;"></i>
                            New student? Create an account
                        </a>
                    </div> -->
                </form>

                <!-- Forgot Password Form -->
                <form class="forgot-form" method="POST" action="{{ route('admin.password.email') }}">
                    @csrf
                    <h2>Reset Password</h2>
                    <p class="login-subtitle">Enter your username or email to receive reset instructions</p>

                    <div id="forgot-msg" style="display:none;margin-bottom:1rem;"></div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <input type="text" id="forgot-email" name="email" class="form-control" required autocomplete="username">
                            <i class="bi bi-person-fill input-icon"></i>
                        </div>
                        <label for="forgot-email">Username or Email</label>
                    </div>

                    <button type="submit" class="btn-login" id="forgot-submit-btn">
                        <span class="btn-text">
                            <i class="bi bi-send" style="margin-right: 0.5rem;"></i>
                            Send Reset Link
                        </span>
                    </button>

                    <div class="processing-message" id="processing-msg">
                        <i class="bi bi-hourglass-split" style="margin-right: 0.25rem;"></i>
                        Processing your request...
                    </div>

                    <div class="back-to-login">
                        <a href="#" id="back-to-login-link">
                            <i class="bi bi-arrow-left" style="margin-right: 0.25rem; font-size: 0.8rem;"></i>
                            Back to Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- SweetAlert bridge -->
    @if(session('status') || session('success'))
    <script>
        window.swalMessage = "{{ session('status') ?? session('success') }}";
        window.swalTitle = "{{ session('status') ? 'Password Reset' : 'Success' }}";
    </script>
    @endif

    @if(session('swalMessage'))
    <script>
        window.swalMessage = "{{ session('swalMessage') }}";
        window.swalTitle = "{{ session('swalTitle') ?? 'Success' }}";
    </script>
    @endif

    @vite(['resources/js/login.js'])

</body>

</html>