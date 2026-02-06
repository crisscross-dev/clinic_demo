@extends('layouts.student')

@section('title', 'Profile Settings')

@section('content')
<style>
    /* Main content responsive layout to work with sidebar - same as dashboard */
    .main-content {
        margin-left: 240px;
        transition: margin-left 0.3s ease;
        min-height: 100vh;
        padding-top: 80px;
    }

    body.collapsed .main-content {
        margin-left: 0;
    }

    @media screen and (max-width: 768px) {
        .main-content {
            margin-left: 0;
            padding-top: 80px;
        }
    }

    /* Adjust container for sidebar layout */
    .container-fluid {
        padding-left: 0;
        padding-right: 0;
    }

    .profile-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 1rem;
    }

    /* Flash card for success messages */
    .flash-card {
        border-radius: 12px;
        padding: 1rem 1.25rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 6px 18px rgba(22, 163, 74, 0.08);
        background: linear-gradient(90deg, rgba(34, 197, 94, 0.08), rgba(16, 185, 129, 0.03));
        border: 1px solid rgba(34, 197, 94, 0.12);
    }

    .flash-card .flash-message {
        display: flex;
        gap: 0.75rem;
        align-items: center;
        color: #064e3b;
        font-weight: 600;
    }

    .flash-card .flash-message p {
        margin: 0;
        font-weight: 500;
        color: #065f46;
        font-size: 0.95rem;
        font-weight: 500;
    }

    .flash-card .flash-close {
        background: transparent;
        border: none;
        font-size: 1.25rem;
        color: #065f46;
        cursor: pointer;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
    }

    .profile-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .profile-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        text-align: center;
    }

    .profile-avatar {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 2rem;
    }

    .section-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        margin-bottom: 1.5rem;
    }

    .section-header {
        background: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
        padding: 1rem 1.5rem;
        font-weight: 600;
        color: #5a5c69;
    }

    .form-label {
        font-weight: 600;
        color: #5a5c69;
        margin-bottom: 0.5rem;
    }

    .form-control {
        border: 2px solid #e3e6f0;
        border-radius: 8px;
        padding: 0.75rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 8px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .info-display {
        background: #f8f9fc;
        border: 2px solid #e3e6f0;
        border-radius: 8px;
        padding: 0.75rem;
        color: #5a5c69;
        font-weight: 500;
    }

    .password-requirements {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 0.5rem;
    }

    .password-requirements ul {
        margin: 0.5rem 0 0 1rem;
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

    .input-wrapper {
        position: relative;
    }

    .input-wrapper .form-control {
        padding-right: 2.5rem;
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
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .profile-container {
            padding: 0.5rem;
        }

        .btn-primary {
            width: 100%;
        }
    }
</style>

<div class="main-content">
    <div class="container-fluid py-4">
        <div class="profile-container">

            @if(session('password_updated'))
            <div class="flash-card" id="passwordUpdatedCard">
                <div class="flash-message">
                    <i class="bi bi-check-circle-fill" style="color:#16a34a; font-size:1.25rem;"></i>
                    <p>Your password has been updated successfully.</p>
                </div>
                <button class="flash-close" id="flashCloseBtn" aria-label="Close">&times;</button>
            </div>
            @endif


            <!-- Account Section (email display) -->
            <div class="section-card mb-3">
                <div class="section-header card-header-lightblue">
                    <i class="bi bi-envelope me-2"></i>
                    Account
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label">Email</label>
                            <div class="info-display bg-gray">{{ optional(auth()->user())->email ?? optional($student)->email ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Password Change Section -->
            <div class="section-card">
                <div class="section-header card-header-lightblue">
                    <i class="bi bi-shield-lock me-2"></i>
                    Change Password
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('student.password.update') }}" method="POST" id="passwordForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <div class="input-wrapper">
                                    <input type="password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        id="current_password"
                                        name="current_password"
                                        placeholder="Enter Current Password"
                                        autocomplete="current-password"
                                        required>
                                    <button type="button" class="toggle-eye-btn" id="toggleCurrentPassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <div class="input-wrapper">
                                    <input type="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        id="password"
                                        name="password"
                                        placeholder="At least 8 characters"
                                        autocomplete="off"
                                        required>
                                    <button type="button" class="toggle-eye-btn" id="togglePassword">
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
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <div class="input-wrapper">
                                    <input type="password"
                                        class="form-control"
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        placeholder="Re-enter new password"
                                        autocomplete="off"
                                        required>
                                    <button type="button" class="toggle-eye-btn" id="togglePasswordConfirm">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="password-match" id="password-match" style="display: none;"></div>
                            </div>
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

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn-general btn-blue">
                                <i class="bi bi-shield-check me-2"></i>
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
            const form = document.getElementById('passwordForm');
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
                            Swal.fire({
                                icon: 'error',
                                title: 'Current Password Required',
                                text: 'Current password is required to change your password.'
                            });
                            currentPasswordInput.focus();
                            return;
                        }

                        // Validate new password
                        const isPasswordValid = validatePassword(password);
                        const isPasswordMatch = validatePasswordMatch();

                        if (!isPasswordValid) {
                            e.preventDefault();
                            togglePasswordHelpers(true);
                            Swal.fire({
                                icon: 'error',
                                title: 'Weak Password',
                                text: 'Please ensure your new password meets all security requirements.'
                            });
                            passwordInput.focus();
                            return;
                        }

                        if (!isPasswordMatch) {
                            e.preventDefault();
                            Swal.fire({
                                icon: 'error',
                                title: 'Password Mismatch',
                                text: 'Password confirmation does not match.'
                            });
                            confirmPasswordInput.focus();
                            return;
                        }
                    }
                });
            }
        });
    </script>

    @if(session('password_updated'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Password Updated',
            text: 'Your password has been successfully updated.',
            timer: 3000,
            showConfirmButton: false
        });
    </script>
    @endif

    <script>
        // Flash card auto-hide and manual close handling
        (function() {
            const card = document.getElementById('passwordUpdatedCard');
            const btn = document.getElementById('flashCloseBtn');
            if (!card) return;

            // Auto-hide after 4 seconds
            setTimeout(() => {
                card.style.transition = 'opacity 300ms ease, transform 300ms ease';
                card.style.opacity = '0';
                card.style.transform = 'translateY(-8px)';
                setTimeout(() => card.remove(), 350);
            }, 4000);

            if (btn) {
                btn.addEventListener('click', () => {
                    card.remove();
                });
            }
        })();
    </script>

    @if($errors->any())
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Please check the form and fix any errors.',
        });
    </script>
    @endif
    @endsection