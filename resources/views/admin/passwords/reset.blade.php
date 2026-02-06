<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Samuel Clinic</title>
    @vite('resources/css/app.css')

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            background: url('{{ asset("images/background.png") }}') no-repeat center center;
            background-size: cover;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Animated background overlay */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg,
                    rgba(30, 87, 153, 0.7) 0%,
                    rgba(32, 124, 202, 0.7) 50%,
                    rgba(30, 87, 153, 0.7) 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            z-index: -1;
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .login-container {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 18px;
            box-shadow: 0 18px 36px rgba(0, 0, 0, 0.14), 0 0 0 1px rgba(255, 255, 255, 0.08);
            overflow: hidden;
            width: 100%;
            max-width: 820px;
            min-height: 520px;
            display: flex;
            position: relative;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-left {
            flex: 1;
            background: linear-gradient(135deg, rgba(30, 87, 153, 0.9) 0%, rgba(32, 124, 202, 0.9) 100%);
            backdrop-filter: blur(10px);
            color: white;
            padding: 2.25rem 1.75rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-right {
            flex: 1;
            padding: 2.25rem 1.75rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a" cx="50%" cy="50%" r="50%"><stop offset="0%" style="stop-color:rgba(255,255,255,0.1);stop-opacity:1" /><stop offset="100%" style="stop-color:rgba(255,255,255,0);stop-opacity:0" /></radialGradient></defs><circle cx="20%" cy="20%" r="10%" fill="url(%23a)" /><circle cx="80%" cy="30%" r="8%" fill="url(%23a)" /><circle cx="30%" cy="70%" r="12%" fill="url(%23a)" /><circle cx="70%" cy="80%" r="6%" fill="url(%23a)" /></svg>') no-repeat;
            background-size: cover;
            animation: float 20s ease-in-out infinite;
            opacity: 0.3;
            z-index: 0;
        }

        @keyframes float {

            0%,
            100% {
                transform: translate(0, 0) rotate(0)
            }

            33% {
                transform: translate(30px, -30px) rotate(120deg)
            }

            66% {
                transform: translate(-20px, 20px) rotate(240deg)
            }
        }

        .clinic-logo {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            margin: 0 auto 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            position: relative;
            z-index: 1;
            transition: all 0.3s ease;
            animation: pulse 3s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4)
            }

            50% {
                transform: scale(1.05);
                box-shadow: 0 0 0 20px rgba(255, 255, 255, 0)
            }
        }

        .login-left h1 {
            font-size: 2rem;
            margin-bottom: 0.75rem;
            font-weight: 700;
            position: relative;
            z-index: 1;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .login-left p {
            font-size: 1rem;
            opacity: 0.95;
            line-height: 1.5;
            position: relative;
            z-index: 1;
            font-weight: 400;
        }

        .login-form h2 {
            color: #1a1a1a;
            margin-bottom: 0.5rem;
            font-size: 1.75rem;
            font-weight: 700;
            text-align: center;
        }

        .login-subtitle {
            color: #6b7280;
            font-size: 0.95rem;
            text-align: center;
            margin-bottom: 1.5rem;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 1.25rem;
            position: relative;
        }

        .form-group label {
            position: absolute;
            left: 2.5rem;
            top: 0.9rem;
            color: #9ca3af;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            pointer-events: none;
            background: transparent;
            z-index: 1;
        }

        .form-group.focused label,
        .form-group.filled label {
            transform: translateY(-2rem) scale(0.85);
            color: #1e5799;
            background: rgba(255, 255, 255, 0.9);
            padding: 0 0.35rem;
            border-radius: 4px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 0.9rem;
            color: #9ca3af;
            font-size: 1rem;
            z-index: 2;
            transition: color 0.3s ease;
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 3.5rem 0.875rem 2.5rem;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 400;
            transition: all 0.3s ease;
            outline: none;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(5px);
        }

        .form-control:focus {
            border-color: #1e5799;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 0 0 4px rgba(30, 87, 153, 0.1);
            transform: translateY(-1px);
        }

        .form-control:focus+.input-icon {
            color: #1e5799;
        }

        .btn-login {
            width: 100%;
            padding: 0.9rem;
            background: linear-gradient(135deg, #1e5799 0%, #207cca 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0.75rem;
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(30, 87, 153, 0.4);
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            border: none;
            font-weight: 500;
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.1));
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #dc2626;
            backdrop-filter: blur(10px);
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(22, 163, 74, 0.1));
            border: 1px solid rgba(34, 197, 94, 0.2);
            color: #16a34a;
            backdrop-filter: blur(10px);
        }

        .back-to-login {
            text-align: center;
            margin-top: 2rem;
        }

        .back-to-login a {
            color: #1e5799;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .back-to-login a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 50%;
            background: #1e5799;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .back-to-login a:hover::after {
            width: 100%;
        }

        .back-to-login a:hover {
            color: #207cca;
        }

        @media (max-width: 768px) {
            body {
                overflow-y: auto;
                align-items: flex-start;
                background-attachment: scroll;
            }

            .login-container {
                flex-direction: column;
                margin: 1rem auto;
                width: 92%;
                max-width: 540px;
                min-height: auto;
                border-radius: 16px;
            }

            .login-left {
                padding: 1.5rem 1.125rem;
                min-height: 220px;
                align-items: center;
                text-align: center;
            }

            .login-right {
                padding: 1.5rem 1.125rem;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .login-left h1 {
                font-size: 1.6rem;
            }

            .clinic-logo {
                width: 64px;
                height: 64px;
                font-size: 1.5rem;
            }

            .form-control {
                padding: 0.8rem 0.8rem 0.8rem 2.25rem;
                font-size: 0.95rem;
                border-radius: 10px;
            }

            .btn-login {
                padding: 0.8rem;
                font-size: 0.95rem;
                border-radius: 10px;
            }
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 0.5rem auto;
                width: 94%;
                max-width: 380px;
                border-radius: 14px;
            }

            .login-left {
                padding: 1rem 0.875rem;
                min-height: 200px;
            }

            .login-right {
                padding: 1rem 0.875rem;
            }

            .login-form h2,
            .login-left h1 {
                font-size: 1.45rem;
            }

            .form-group {
                margin-bottom: 0.875rem;
            }

            .form-group label {
                left: 2rem;
                top: 0.7rem;
                font-size: 0.85rem;
            }

            .form-control {
                padding: 0.7rem 0.75rem 0.7rem 2rem;
                font-size: 0.9rem;
                border-radius: 9px;
            }

            .input-icon {
                left: 0.75rem;
                font-size: 0.95rem;
            }

            .btn-login {
                padding: 0.75rem;
                font-size: 0.9rem;
                border-radius: 9px;
            }
        }

        /* Password Validation Styles */
        .password-requirements {
            background: rgba(59, 130, 246, 0.05);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .password-requirements h4 {
            color: #1e40af;
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .password-requirements ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .requirement {
            display: flex;
            align-items: center;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        .requirement:last-child {
            margin-bottom: 0;
        }

        .requirement i {
            margin-right: 0.5rem;
            font-size: 0.75rem;
            transition: all 0.3s ease;
        }

        .requirement.valid {
            color: #16a34a;
        }

        .requirement.valid i {
            color: #16a34a;
        }

        .requirement.valid i {
            color: #16a34a;
        }

        .requirement.valid i.bi-x-circle-fill:before {
            content: "\f26a";
            /* bi-check-circle-fill */
        }

        .requirement:not(.valid) {
            color: #dc2626;
        }

        .requirement:not(.valid) i {
            color: #dc2626;
        }

        .password-toggle {
            position: absolute;
            right: 0.5rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            z-index: 3;
            padding: 0.6rem;
            border-radius: 6px;
            min-width: 2.5rem;
            min-height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle:hover {
            color: #1e5799;
            background: rgba(59, 130, 246, 0.1);
            transform: translateY(-50%) scale(1.05);
        }

        .password-toggle:focus {
            outline: none;
            color: #1e5799;
            background: rgba(59, 130, 246, 0.15);
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
        }

        .password-toggle:active {
            transform: translateY(-50%) scale(0.95);
            background: rgba(59, 130, 246, 0.2);
        }

        .password-strength {
            margin-top: 0.5rem;
        }

        .strength-bar {
            width: 100%;
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-fill.weak {
            width: 25%;
            background: #dc2626;
        }

        .strength-fill.fair {
            width: 50%;
            background: #f59e0b;
        }

        .strength-fill.good {
            width: 75%;
            background: #3b82f6;
        }

        .strength-fill.strong {
            width: 100%;
            background: #16a34a;
        }

        .strength-text {
            font-size: 0.8rem;
            margin-top: 0.25rem;
            display: block;
            font-weight: 500;
        }

        .strength-text.weak {
            color: #dc2626;
        }

        .strength-text.fair {
            color: #f59e0b;
        }

        .strength-text.good {
            color: #3b82f6;
        }

        .strength-text.strong {
            color: #16a34a;
        }

        .password-match {
            font-size: 0.8rem;
            margin-top: 0.25rem;
            font-weight: 500;
            min-height: 1rem;
        }

        .password-match.match {
            color: #16a34a;
        }

        .password-match.no-match {
            color: #dc2626;
        }

        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-login:disabled:hover {
            transform: none;
            box-shadow: none;
        }

        .form-control.is-invalid {
            border-color: #dc2626;
        }

        .form-control.is-valid {
            border-color: #16a34a;
        }

        .invalid-feedback {
            color: #dc2626;
            font-size: 0.8rem;
            margin-top: 0.25rem;
            display: block;
        }

        @media (max-width: 768px) {
            .password-requirements {
                padding: 0.75rem;
                margin-bottom: 1rem;
            }

            .requirement {
                font-size: 0.8rem;
            }
        }

        @media (max-width: 360px) {
            .login-container {
                width: 96%;
                max-width: 320px;
                margin: 0.5rem auto;
            }

            .clinic-logo {
                width: 56px;
                height: 56px;
                font-size: 1.3rem;
            }

            .login-form h2,
            .login-left h1 {
                font-size: 1.3rem;
            }

            .form-control {
                padding: 0.65rem 2.5rem 0.65rem 1.85rem;
                font-size: 0.875rem;
                border-radius: 8px;
            }

            .btn-login {
                padding: 0.7rem;
                font-size: 0.875rem;
                border-radius: 8px;
            }

            .password-requirements {
                padding: 0.5rem;
            }

            .requirement {
                font-size: 0.75rem;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Floating labels
            const formGroups = document.querySelectorAll('.form-group');
            formGroups.forEach(group => {
                const input = group.querySelector('.form-control');
                if (!input) return;
                if (input.value) group.classList.add('filled');
                input.addEventListener('focus', () => group.classList.add('focused'));
                input.addEventListener('blur', () => {
                    group.classList.remove('focused');
                    if (input.value) group.classList.add('filled');
                    else group.classList.remove('filled');
                });
                input.addEventListener('input', () => input.value ? group.classList.add('filled') : group.classList.remove('filled'));
            });

            // Password validation
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');
            const submitBtn = document.getElementById('submitBtn');
            const strengthFill = document.getElementById('strength-fill');
            const strengthText = document.getElementById('strength-text');
            const passwordMatch = document.getElementById('password-match');

            // Password requirements elements
            const lengthCheck = document.getElementById('length-check');
            const uppercaseCheck = document.getElementById('uppercase-check');
            const lowercaseCheck = document.getElementById('lowercase-check');
            const numberCheck = document.getElementById('number-check');
            const specialCheck = document.getElementById('special-check');

            // Password toggle functionality
            const togglePassword = document.getElementById('togglePassword');
            const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');

            function togglePasswordVisibility(input, toggleBtn) {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                const icon = toggleBtn.querySelector('i');

                if (type === 'text') {
                    icon.classList.remove('bi-eye-fill');
                    icon.classList.add('bi-eye-slash-fill');
                    toggleBtn.setAttribute('aria-label', 'Hide password');
                } else {
                    icon.classList.remove('bi-eye-slash-fill');
                    icon.classList.add('bi-eye-fill');
                    toggleBtn.setAttribute('aria-label', 'Show password');
                }
            }

            if (togglePassword) {
                togglePassword.addEventListener('click', () => {
                    togglePasswordVisibility(passwordInput, togglePassword);
                });
            }

            if (togglePasswordConfirm) {
                togglePasswordConfirm.addEventListener('click', () => {
                    togglePasswordVisibility(confirmPasswordInput, togglePasswordConfirm);
                });
            }

            // Password validation function
            function validatePassword(password) {
                const requirements = {
                    length: password.length >= 8,
                    uppercase: /[A-Z]/.test(password),
                    lowercase: /[a-z]/.test(password),
                    number: /\d/.test(password),
                    special: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)
                };

                // Update requirement checkmarks
                lengthCheck.classList.toggle('valid', requirements.length);
                uppercaseCheck.classList.toggle('valid', requirements.uppercase);
                lowercaseCheck.classList.toggle('valid', requirements.lowercase);
                numberCheck.classList.toggle('valid', requirements.number);
                specialCheck.classList.toggle('valid', requirements.special);

                // Calculate strength
                const validCount = Object.values(requirements).filter(Boolean).length;
                let strength = 'weak';
                let strengthLabel = 'Weak';

                if (validCount === 5) {
                    strength = 'strong';
                    strengthLabel = 'Strong';
                } else if (validCount >= 4) {
                    strength = 'good';
                    strengthLabel = 'Good';
                } else if (validCount >= 3) {
                    strength = 'fair';
                    strengthLabel = 'Fair';
                }

                // Update strength indicator
                strengthFill.className = `strength-fill ${strength}`;
                strengthText.className = `strength-text ${strength}`;
                strengthText.textContent = `Password strength: ${strengthLabel}`;

                // Update input styling
                if (validCount === 5) {
                    passwordInput.classList.remove('is-invalid');
                    passwordInput.classList.add('is-valid');
                } else if (password.length > 0) {
                    passwordInput.classList.remove('is-valid');
                    passwordInput.classList.add('is-invalid');
                } else {
                    passwordInput.classList.remove('is-valid', 'is-invalid');
                }

                return validCount === 5;
            }

            // Password confirmation validation
            function validatePasswordMatch() {
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                if (!confirmPassword) {
                    passwordMatch.textContent = '';
                    passwordMatch.className = 'password-match';
                    confirmPasswordInput.classList.remove('is-valid', 'is-invalid');
                    return false;
                }

                if (password === confirmPassword) {
                    passwordMatch.textContent = '✓ Passwords match';
                    passwordMatch.className = 'password-match match';
                    confirmPasswordInput.classList.remove('is-invalid');
                    confirmPasswordInput.classList.add('is-valid');
                    return true;
                } else {
                    passwordMatch.textContent = '✗ Passwords do not match';
                    passwordMatch.className = 'password-match no-match';
                    confirmPasswordInput.classList.remove('is-valid');
                    confirmPasswordInput.classList.add('is-invalid');
                    return false;
                }
            }

            // Enable/disable submit button
            function updateSubmitButton() {
                const isPasswordValid = validatePassword(passwordInput.value);
                const isPasswordMatch = validatePasswordMatch();
                submitBtn.disabled = !(isPasswordValid && isPasswordMatch);
            }

            // Event listeners
            passwordInput.addEventListener('input', function() {
                validatePassword(this.value);
                if (confirmPasswordInput.value) {
                    validatePasswordMatch();
                }
                updateSubmitButton();
            });

            confirmPasswordInput.addEventListener('input', function() {
                validatePasswordMatch();
                updateSubmitButton();
            });

            // Form submission validation
            document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
                const isPasswordValid = validatePassword(passwordInput.value);
                const isPasswordMatch = validatePasswordMatch();

                if (!isPasswordValid || !isPasswordMatch) {
                    e.preventDefault();
                    alert('Please ensure your password meets all requirements and matches the confirmation.');
                }
            });

            // Initial validation
            updateSubmitButton();
        });
    </script>
</head>

<body>
    <div class="login-container">
        <!-- Left Side - Branding -->
        <div class="login-left">
            <div class="clinic-logo">
                <img src="{{ asset('icon/key.svg') }}" alt="Reset Password" width="40" height="40">
            </div>
            <h1>Reset Password</h1>
            <p>{{ isset($userType) && $userType === 'student' ? 'Student' : 'Admin' }} Password Recovery</p>
            <p>Create a new secure password for your {{ isset($userType) && $userType === 'student' ? 'student' : 'admin' }} account.</p>
        </div>

        <!-- Right Side - Form -->
        <div class="login-right">
            <div class="login-form">
                <h2>Create New Password</h2>
                <p class="login-subtitle">Enter your new password below</p>

                @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
                @endif

                @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif

                <form method="POST" action="{{ isset($userType) && $userType === 'student' ? route('student.password.reset.submit') : route('admin.password.update') }}" id="resetPasswordForm">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">

                    <!-- Password Requirements Display -->
                    <div class="password-requirements">
                        <h4>Password Requirements:</h4>
                        <ul id="password-checklist">
                            <li id="length-check" class="requirement">
                                <i class="bi bi-x-circle-fill"></i>
                                At least 8 characters long
                            </li>
                            <li id="uppercase-check" class="requirement">
                                <i class="bi bi-x-circle-fill"></i>
                                One uppercase letter (A-Z)
                            </li>
                            <li id="lowercase-check" class="requirement">
                                <i class="bi bi-x-circle-fill"></i>
                                One lowercase letter (a-z)
                            </li>
                            <li id="number-check" class="requirement">
                                <i class="bi bi-x-circle-fill"></i>
                                One number (0-9)
                            </li>
                            <li id="special-check" class="requirement">
                                <i class="bi bi-x-circle-fill"></i>
                                One special character (!@#$%^&*)
                            </li>
                        </ul>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
                            <i class="bi bi-lock-fill input-icon"></i>
                            <button type="button" class="password-toggle" id="togglePassword" aria-label="Toggle password visibility">
                                <i class="bi bi-eye-fill"></i>
                            </button>
                        </div>
                        <label for="password">New Password</label>
                        <div class="password-strength">
                            <div class="strength-bar">
                                <div class="strength-fill" id="strength-fill"></div>
                            </div>
                            <span class="strength-text" id="strength-text">Password strength</span>
                        </div>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required autocomplete="new-password">
                            <i class="bi bi-lock-fill input-icon"></i>
                            <button type="button" class="password-toggle" id="togglePasswordConfirm" aria-label="Toggle password confirmation visibility">
                                <i class="bi bi-eye-fill"></i>
                            </button>
                        </div>
                        <label for="password_confirmation">Confirm Password</label>
                        <div class="password-match" id="password-match"></div>
                        @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn-login" id="submitBtn" disabled>
                        <i class="bi bi-key-fill" style="margin-right: 0.5rem;"></i>
                        Reset Password
                    </button>
                </form>

                <div class="back-to-login">
                    <a href="{{ route('admin.login') }}">
                        <i class="bi bi-arrow-left" style="margin-right: 0.25rem; font-size: 0.8rem;"></i>
                        Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>