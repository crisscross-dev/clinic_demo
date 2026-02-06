@extends('layouts.app')
@section('title', 'Admin Details - ' . $admin->firstname . ' ' . $admin->lastname)

@php
$isDemo = session('is_demo', false) || strtolower(session('admin_username', '')) === 'demo@gmail.com';
@endphp

@push('styles')
<style>
    /* Demo Mode Styles */
    .demo-banner {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        color: white;
        padding: 12px 20px;
        border-radius: 12px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        font-weight: 600;
        font-size: 1rem;
        box-shadow: 0 4px 15px rgba(238, 90, 36, 0.3);
        animation: pulse-demo 2s infinite;
    }

    .demo-banner i {
        font-size: 1.3rem;
    }

    @keyframes pulse-demo {

        0%,
        100% {
            box-shadow: 0 4px 15px rgba(238, 90, 36, 0.3);
        }

        50% {
            box-shadow: 0 4px 25px rgba(238, 90, 36, 0.5);
        }
    }

    .demo-mode .form-control {
        background-color: #f8f9fa !important;
        cursor: not-allowed;
        opacity: 0.7;
    }

    .demo-mode .form-control:focus {
        box-shadow: none;
        border-color: #ccc;
    }

    .demo-mode .input-group-text {
        background-color: #e9ecef;
        opacity: 0.7;
    }

    .demo-overlay {
        position: relative;
    }

    .demo-overlay::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: transparent;
        cursor: not-allowed;
        z-index: 10;
    }

    .settings-page {
        background-color: #f5f7fa;
        min-height: 100vh;
        padding: 40px 20px;
    }

    .form-section {
        border-radius: 16px;
        border: 1px solid #e5e9f2;
        background: #fff;
    }

    .section-title h3 {
        font-weight: 700;
        font-size: 1.3rem;
        color: #222;
        margin-bottom: 1rem;
        letter-spacing: 0.5px;
    }

    .section-divider {
        border-top: 1px solid #eaeaea;
        margin: 1.5rem 0 1rem 0;
        padding-top: 1rem;
    }

    .section-divider h5 {
        font-size: 1rem;
        font-weight: 600;
        color: #555;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-label {
        font-weight: 500;
        font-size: 0.9rem;
        color: #444;
    }

    .form-control {
        background-color: #f1f3f6;
        color: #333;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.08);
        border: 1px solid #ccc;
    }

    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.15);
    }

    .btn-primary {
        border-radius: 10px;
        padding: 10px 24px;
        font-weight: 600;
        transition: 0.2s ease-in-out;
    }

    .btn-primary:hover {
        background-color: #0b5ed7;
    }

    .btn-outline-secondary {
        border-radius: 10px;
        padding: 10px 24px;
        transition: 0.2s ease-in-out;
    }

    .btn-outline-secondary:hover {
        background-color: #f1f1f1;
    }

    .alert {
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 8px;
        animation: fadeIn 0.4s ease-in-out;
    }

    .alert-success {
        background-color: #e9f7ef;
        color: #2d7a46;
        border: 1px solid #cdebd7;
    }

    .alert-danger {
        background-color: #fdecea;
        color: #b94a48;
        border: 1px solid #f5c2c0;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .input-wrapper {
        position: relative;
    }

    .input-wrapper .form-control {
        padding-right: 2.5rem;
        /* make space for the eye */
    }

    .input-wrapper .toggle-eye-btn {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: transparent;
        cursor: pointer;
        color: #666;
        font-size: 1.1rem;
        display: none;
        /* hidden by default */
    }

    /* Password Validation Styles */
    .password-requirements {
        background: rgba(59, 130, 246, 0.05);
        border: 1px solid rgba(59, 130, 246, 0.2);
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .password-requirements h6 {
        color: #1e40af;
        font-size: 0.9rem;
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
        font-size: 0.8rem;
        margin-bottom: 0.4rem;
        transition: all 0.3s ease;
    }

    .requirement:last-child {
        margin-bottom: 0;
    }

    .requirement i {
        margin-right: 0.5rem;
        font-size: 0.7rem;
        transition: all 0.3s ease;
    }

    .requirement.valid {
        color: #16a34a;
    }

    .requirement.valid i {
        color: #16a34a;
    }

    .requirement.valid i::before {
        content: "\f058";
        /* fa-check-circle */
    }

    .requirement:not(.valid) {
        color: #dc2626;
    }

    .requirement:not(.valid) i {
        color: #dc2626;
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
        font-size: 0.75rem;
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
        font-size: 0.75rem;
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
</style>
@endpush


@section('content')
<div class="main-content settings-page">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <main class="col-12">
                @if($isDemo)
                <div class="demo-banner">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>DEMO MODE - All changes are disabled for this demonstration account</span>
                </div>
                @endif
                <div class="form-section card shadow-sm p-4 w-100 {{ $isDemo ? 'demo-mode' : '' }}">

                    <div class="card-body">
                        <div class="section-title d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">SETTINGS</h3>
                            @if(session('admin_role') === 'admin')
                            <a href="{{ route('admins.index') }}"
                                class="btn-general btn-orange d-flex align-items-center justify-content-center"
                                title="Admin Management">

                                <i class="bi bi-shield-exclamation me-2"></i>
                                <!-- Mobile: short text -->
                                <span class="d-inline d-sm-none">Admin</span>
                                <!-- Desktop: full text -->
                                <span class="d-none d-sm-inline">Admin Management</span>
                            </a>
                            @endif
                        </div>

                        <form id="adminForm" method="POST" action="{{ route('admins.update', $admin) }}">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-12 section-divider">
                                    <h5>PROFILE</h5>
                                </div>

                                <!-- Username -->
                                <div class="col-md-12">
                                    <label class="form-label">Username</label>
                                    <div class="input-group">
                                        <span class="input-group-text">@</span>
                                        <input id="username" name="username" type="text"
                                            class="form-control @error('username') is-invalid @enderror"
                                            placeholder="your user name"
                                            value="{{ old('username', $admin->username) }}"
                                            {{ $isDemo ? 'readonly' : '' }}>
                                    </div>
                                    @error('username')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Firstname -->
                                <div class="col-md-6">
                                    <label class="form-label">First name</label>
                                    <input id="firstname" name="firstname" type="text"
                                        class="form-control @error('firstname') is-invalid @enderror"
                                        placeholder="Enter first name"
                                        value="{{ old('firstname', $admin->firstname) }}"
                                        {{ $isDemo ? 'readonly' : '' }}>
                                    @error('firstname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Lastname -->
                                <div class="col-md-6">
                                    <label class="form-label">Last name</label>
                                    <input id="lastname" name="lastname" type="text"
                                        class="form-control @error('lastname') is-invalid @enderror"
                                        placeholder="Enter last name"
                                        value="{{ old('lastname', $admin->lastname) }}"
                                        {{ $isDemo ? 'readonly' : '' }}>
                                    @error('lastname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Middlename -->
                                <div class="col-md-6">
                                    <label class="form-label">Middle name</label>
                                    <input id="middlename" name="middlename" type="text"
                                        class="form-control @error('middlename') is-invalid @enderror"
                                        placeholder="Enter middle name"
                                        value="{{ old('middlename', $admin->middlename) }}"
                                        {{ $isDemo ? 'readonly' : '' }}>
                                    @error('middlename')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Prefix</label>
                                    <input id="prefix" name="prefix" type="text"
                                        class="form-control @error('prefix') is-invalid @enderror"
                                        placeholder="NIR, DR. , MR. , MS. , MRS"
                                        value="{{ old('prefix', $admin->prefix) }}"
                                        {{ $isDemo ? 'readonly' : '' }}>
                                    @error('prefix')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <!-- Divider between identity and password -->
                                <div class="col-12 section-divider">
                                    <h5>PASSWORD</h5>
                                </div>

                                <!-- Password Requirements Display -->
                                <div class="col-12 password-requirements" id="passwordRequirements" style="display: none;">
                                    <h6>Password Requirements:</h6>
                                    <ul id="password-checklist">
                                        <li id="length-check" class="requirement">
                                            <i class="bi bi-circle-fill"></i>
                                            At least 8 characters long
                                        </li>
                                        <li id="uppercase-check" class="requirement">
                                            <i class="bi bi-circle-fill"></i>
                                            One uppercase letter (A-Z)
                                        </li>
                                        <li id="lowercase-check" class="requirement">
                                            <i class="bi bi-circle-fill"></i>
                                            One lowercase letter (a-z)
                                        </li>
                                        <li id="number-check" class="requirement">
                                            <i class="bi bi-circle-fill"></i>
                                            One number (0-9)
                                        </li>
                                        <li id="special-check" class="requirement">
                                            <i class="bi bi-circle-fill"></i>
                                            One special character (!@#$%^&*)
                                        </li>
                                    </ul>
                                </div>

                                <!-- Current Password -->
                                <div class="col-md-4">
                                    <label class="form-label">Current password</label>
                                    <div class="input-wrapper">
                                        <input id="current_password" placeholder="Enter current password" name="current_password" type="password"
                                            class="form-control @error('current_password') is-invalid @enderror"
                                            autocomplete="current-password" value=""
                                            {{ $isDemo ? 'readonly' : '' }}>
                                        <button type="button" class="toggle-eye-btn" id="toggleCurrentPassword" {{ $isDemo ? 'disabled' : '' }}>
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    @error('current_password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- New Password -->
                                <div class="col-md-4">
                                    <label class="form-label">New password</label>
                                    <div class="input-wrapper">
                                        <input id="password" name="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            autocomplete="off"
                                            placeholder="Enter new password"
                                            {{ $isDemo ? 'readonly' : '' }}>
                                        <button type="button" class="toggle-eye-btn" id="togglePassword" {{ $isDemo ? 'disabled' : '' }}>
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    <div class="password-strength" id="passwordStrength" style="display: none;">
                                        <div class="strength-bar">
                                            <div class="strength-fill" id="strength-fill"></div>
                                        </div>
                                        <span class="strength-text" id="strength-text">Password strength</span>
                                    </div>
                                    @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Confirm New Password -->
                                <div class="col-md-4">
                                    <label class="form-label">Confirm new password</label>
                                    <div class="input-wrapper">
                                        <input id="password_confirmation" name="password_confirmation" type="password"
                                            class="form-control"
                                            autocomplete="off"
                                            placeholder="Confirm new password"
                                            {{ $isDemo ? 'readonly' : '' }}>
                                        <button type="button" class="toggle-eye-btn" id="togglePasswordConfirm" {{ $isDemo ? 'disabled' : '' }}>
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    <div class="password-match" id="password-match" style="display: none;"></div>
                                    @error('password_confirmation')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>




                                <!-- Buttons -->
                                <div class="col-12 d-flex gap-2 justify-content-center mt-3">
                                    @if($isDemo)
                                    <button type="button" class="btn-general btn-blue" disabled title="Demo account is read-only">
                                        <i class="bi bi-lock-fill me-2"></i>Save changes (Demo Locked)
                                    </button>
                                    @else
                                    <button type="submit" class="btn-general btn-blue">
                                        <i class="bi bi-check-circle me-2"></i>Save changes
                                    </button>
                                    @endif
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Password validation elements
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password_confirmation');
        const currentPasswordInput = document.getElementById('current_password');
        const strengthFill = document.getElementById('strength-fill');
        const strengthText = document.getElementById('strength-text');
        const passwordMatch = document.getElementById('password-match');
        const passwordRequirements = document.getElementById('passwordRequirements');
        const passwordStrength = document.getElementById('passwordStrength');

        // Password requirements elements
        const lengthCheck = document.getElementById('length-check');
        const uppercaseCheck = document.getElementById('uppercase-check');
        const lowercaseCheck = document.getElementById('lowercase-check');
        const numberCheck = document.getElementById('number-check');
        const specialCheck = document.getElementById('special-check');

        // Password toggle functionality
        function toggleVisibility(input, button) {
            if (!input || !button) return;
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            button.innerHTML = isHidden ?
                '<i class="bi bi-eye-slash"></i>' :
                '<i class="bi bi-eye"></i>';
        }

        function attachToggle(inputId, btnId) {
            const input = document.getElementById(inputId);
            const button = document.getElementById(btnId);

            if (!input || !button) return;

            // Show/hide eye based on input value
            input.addEventListener('input', function() {
                button.style.display = this.value.length > 0 ? 'block' : 'none';
            });

            // Toggle password visibility
            button.addEventListener('click', function() {
                toggleVisibility(input, this);
            });
        }

        attachToggle('password', 'togglePassword');
        attachToggle('password_confirmation', 'togglePasswordConfirm');
        attachToggle('current_password', 'toggleCurrentPassword');

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
            if (lengthCheck) lengthCheck.classList.toggle('valid', requirements.length);
            if (uppercaseCheck) uppercaseCheck.classList.toggle('valid', requirements.uppercase);
            if (lowercaseCheck) lowercaseCheck.classList.toggle('valid', requirements.lowercase);
            if (numberCheck) numberCheck.classList.toggle('valid', requirements.number);
            if (specialCheck) specialCheck.classList.toggle('valid', requirements.special);

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
            if (strengthFill) {
                strengthFill.className = `strength-fill ${strength}`;
            }
            if (strengthText) {
                strengthText.className = `strength-text ${strength}`;
                strengthText.textContent = `Password strength: ${strengthLabel}`;
            }

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
                if (passwordMatch) {
                    passwordMatch.textContent = '';
                    passwordMatch.className = 'password-match';
                    passwordMatch.style.display = 'none';
                }
                confirmPasswordInput.classList.remove('is-valid', 'is-invalid');
                return false;
            }

            if (passwordMatch) {
                passwordMatch.style.display = 'block';
            }

            if (password === confirmPassword && password.length > 0) {
                if (passwordMatch) {
                    passwordMatch.textContent = '✓ Passwords match';
                    passwordMatch.className = 'password-match match';
                }
                confirmPasswordInput.classList.remove('is-invalid');
                confirmPasswordInput.classList.add('is-valid');
                return true;
            } else {
                if (passwordMatch) {
                    passwordMatch.textContent = '✗ Passwords do not match';
                    passwordMatch.className = 'password-match no-match';
                }
                confirmPasswordInput.classList.remove('is-valid');
                confirmPasswordInput.classList.add('is-invalid');
                return false;
            }
        }

        // Show/hide password requirements and strength indicator
        function togglePasswordHelpers(show) {
            if (passwordRequirements) {
                passwordRequirements.style.display = show ? 'block' : 'none';
            }
            if (passwordStrength) {
                passwordStrength.style.display = show ? 'block' : 'none';
            }
        }

        // Event listeners
        if (passwordInput) {
            passwordInput.addEventListener('focus', function() {
                togglePasswordHelpers(true);
            });

            passwordInput.addEventListener('input', function() {
                const hasValue = this.value.length > 0;
                togglePasswordHelpers(hasValue);

                if (hasValue) {
                    validatePassword(this.value);
                    if (confirmPasswordInput.value) {
                        validatePasswordMatch();
                    }
                } else {
                    // Clear validation when password is empty
                    this.classList.remove('is-valid', 'is-invalid');
                    if (confirmPasswordInput.value) {
                        validatePasswordMatch();
                    }
                }
            });

            passwordInput.addEventListener('blur', function() {
                if (!this.value) {
                    togglePasswordHelpers(false);
                }
            });
        }

        if (confirmPasswordInput) {
            confirmPasswordInput.addEventListener('input', function() {
                validatePasswordMatch();
            });
        }

        // Validate current password requirement
        if (currentPasswordInput && passwordInput) {
            passwordInput.addEventListener('input', function() {
                if (this.value && !currentPasswordInput.value) {
                    currentPasswordInput.classList.add('is-invalid');
                } else if (currentPasswordInput.classList.contains('is-invalid') && currentPasswordInput.value) {
                    currentPasswordInput.classList.remove('is-invalid');
                }
            });

            currentPasswordInput.addEventListener('input', function() {
                if (this.value && this.classList.contains('is-invalid')) {
                    this.classList.remove('is-invalid');
                }
            });
        }

        // Form submission validation
        const form = document.getElementById('adminForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;
                const currentPassword = currentPasswordInput.value;

                // If user is trying to change password
                if (password || confirmPassword) {
                    // Check if current password is provided
                    if (!currentPassword) {
                        e.preventDefault();
                        currentPasswordInput.classList.add('is-invalid');
                        alert('Current password is required to change your password.');
                        currentPasswordInput.focus();
                        return;
                    }

                    // Validate new password
                    const isPasswordValid = validatePassword(password);
                    const isPasswordMatch = validatePasswordMatch();

                    if (!isPasswordValid) {
                        e.preventDefault();
                        togglePasswordHelpers(true);
                        alert('Please ensure your new password meets all security requirements.');
                        passwordInput.focus();
                        return;
                    }

                    if (!isPasswordMatch) {
                        e.preventDefault();
                        alert('Password confirmation does not match.');
                        confirmPasswordInput.focus();
                        return;
                    }
                }
            });
        }
    });
</script>
@endpush