<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DEMO - Student Registration</title>
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
    @vite(['resources/css/app.css', 'resources/css/login_register.css', 'resources/js/login_register.js'])

</head>

<body>
    <div class="register-container">
        <!-- Left Side - Branding -->
        <div class="register-left">
            <div class="clinic-logo">
                <img src="{{ asset('images/logo2_pdf.png') }}" alt="Samuel Clinic Logo" width="80" height="70">
            </div>
            <h1>Register DEMO Clinic</h1>
            <p>Student Registration</p>
            <p>Create your account to access our healthcare management system and book appointments.</p>
        </div>

        <!-- Right Side - Registration Form -->
        <div class="register-right">
            <form class="register-form" method="POST" action="{{ route('student.register') }}">
                @csrf
                <h2>Create Account</h2>

                @if ($errors->any())
                <div class="alert alert-danger" style="display: flex; flex-direction: column; gap: 0.3rem;">
                    @foreach ($errors->all() as $error)
                    <div style="display: flex; align-items: center;">
                        <i class="bi bi-exclamation-triangle-fill" style="color: #b71c1c; margin-right: 0.5rem;"></i>
                        <span>{{ $error }}</span>
                    </div>
                    @endforeach
                </div>
                @endif



                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="text" id="first_name" name="first_name" class="form-control" value="{{ old('first_name') }}" required placeholder="">
                        <i class="bi bi-person-fill input-icon"></i>
                    </div>
                    <label for="first_name">First Name</label>
                </div>

                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="text" id="middle_name" name="middle_name" class="form-control" value="{{ old('middle_name') }}" placeholder="">
                        <i class="bi bi-person-fill input-icon"></i>
                    </div>
                    <label for="middle_name">Middle Name</label>
                </div>

                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="text" id="last_name" name="last_name" class="form-control" value="{{ old('last_name') }}" required placeholder="">
                        <i class="bi bi-person-fill input-icon"></i>
                    </div>
                    <label for="last_name">Last Name</label>
                </div>

                <div class="form-group" style="position: relative;">
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="">
                        <i class="bi bi-envelope-fill input-icon"></i>
                    </div>
                    <label for="email">Email Address</label>
                    <div class="email-hint" id="email-hint">
                        <i class="bi bi-info-circle-fill"></i>
                        <span>Please use a valid email address</span>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-wrapper password-field-wrapper">
                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required placeholder="">
                        <i class="bi bi-lock-fill input-icon"></i>
                        <button type="button" class="password-toggle" aria-label="Toggle password visibility">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                    </div>
                    <label for="password">Password</label>
                    <div id="password-feedback" class="invalid-feedback" style="display:none; margin-top:0.5rem;"></div>
                    @error('password')
                    <div class="invalid-feedback" style="display:block; margin-top:0.5rem;">
                        <i class="bi bi-exclamation-circle-fill" style="margin-right: 0.3rem;"></i>{{ $message }}
                    </div>
                    @enderror

                    <!-- Password Requirements Tooltip -->
                    <div class="password-requirements">
                        <h4><i class="bi bi-shield-check"></i>Password Requirements</h4>
                        <ul>
                            <li class="requirement" id="length-check">
                                <i class="bi bi-x-circle-fill"></i>
                                <span>At least 8 characters</span>
                            </li>
                            <li class="requirement" id="uppercase-check">
                                <i class="bi bi-x-circle-fill"></i>
                                <span>One uppercase letter (A-Z)</span>
                            </li>
                            <li class="requirement" id="lowercase-check">
                                <i class="bi bi-x-circle-fill"></i>
                                <span>One lowercase letter (a-z)</span>
                            </li>
                            <li class="requirement" id="number-check">
                                <i class="bi bi-x-circle-fill"></i>
                                <span>One number (0-9)</span>
                            </li>
                            <li class="requirement" id="special-check">
                                <i class="bi bi-x-circle-fill"></i>
                                <span>One special character (!@#$%^&*)</span>
                            </li>
                        </ul>
                        <div class="password-strength">
                            <div class="strength-bar">
                                <div class="strength-fill" id="strength-fill"></div>
                            </div>
                            <span class="strength-text" id="strength-text">Password strength: Weak</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-wrapper password-field-wrapper">
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control @error('password') is-invalid @enderror" required placeholder="">
                        <i class="bi bi-lock-fill input-icon"></i>
                        <button type="button" class="password-toggle" aria-label="Toggle password visibility">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                    </div>
                    <label for="password_confirmation">Confirm Password</label>
                    <div class="password-match" id="password-match"></div>
                </div>

                <button type="submit" class="btn-register">
                    <i class="bi bi-person-plus-fill" style="margin-right: 0.5rem;"></i>
                    Create Account
                </button>

                <div class="login-link">
                    <a href="{{ route('unified.login') }}">
                        <i class="bi bi-arrow-left" style="margin-right: 0.25rem; font-size: 0.8rem;"></i>
                        Already have an account? Sign in
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>