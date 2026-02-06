// ✅ Top import for Vite
import Swal from "sweetalert2";

document.addEventListener("DOMContentLoaded", () => {
    const $ = (sel) => document.querySelector(sel);
    const $$ = (sel) => document.querySelectorAll(sel);

    /* -------------------------------------------
     * Floating Labels
     * ------------------------------------------- */
    $$(".form-group").forEach((group) => {
        const input = group.querySelector(".form-control");
        if (!input) return;

        const updateState = () => {
            group.classList.toggle("filled", !!input.value);
        };

        input.addEventListener("focus", () => group.classList.add("focused"));
        input.addEventListener("blur", () => group.classList.remove("focused"));
        input.addEventListener("input", updateState);
        updateState();
    });

    /* -------------------------------------------
     * Password Toggle
     * ------------------------------------------- */
    $$(".password-toggle").forEach((btn) => {
        btn.addEventListener("click", () => {
            const input = btn.parentElement.querySelector("input");
            const icon = btn.querySelector("i");
            const isPassword = input.type === "password";

            input.type = isPassword ? "text" : "password";
            icon.classList.toggle("bi-eye-fill", !isPassword);
            icon.classList.toggle("bi-eye-slash-fill", isPassword);
            btn.setAttribute(
                "aria-label",
                isPassword ? "Hide password" : "Show password"
            );
        });
    });

    /* -------------------------------------------
     * Email Validation
     * ------------------------------------------- */
    const emailInput = document.getElementById("email");
    const emailHint = document.getElementById("email-hint");

    const validateEmail = (email) => {
        if (!email || !email.includes("@")) return false;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    };

    if (emailInput) {
        emailInput.addEventListener("input", () => {
            const email = emailInput.value.trim();

            // Hide server error (Bootstrap alert)
            const alertDanger = document.querySelector(".alert-danger");
            if (alertDanger) alertDanger.style.display = "none";

            // Remove Laravel invalid class
            emailInput.classList.remove("is-invalid");

            if (!email) {
                emailInput.style.borderColor = "#e5e7eb";
                emailHint?.classList.remove("valid");
                return;
            }

            if (validateEmail(email)) {
                emailInput.style.borderColor = "#10b981"; // green
                emailHint?.classList.add("valid");
            } else {
                emailInput.style.borderColor = "#ef4444"; // red
                emailHint?.classList.remove("valid");
            }
        });
    }


    /* -------------------------------------------
     * Password Strength & Validation
     * ------------------------------------------- */
    const passwordInput = $("#password");
    const confInput = $("#password_confirmation");
    const pwFeedback = $("#password-feedback");
    const registerForm = $(".register-form");

    // Password requirement elements
    const lengthCheck = $("#length-check");
    const uppercaseCheck = $("#uppercase-check");
    const lowercaseCheck = $("#lowercase-check");
    const numberCheck = $("#number-check");
    const specialCheck = $("#special-check");
    const strengthFill = $("#strength-fill");
    const strengthText = $("#strength-text");
    const passwordMatch = $("#password-match");

    const checkStrength = (pw) => {
        let score = 0;
        if (pw.length >= 8) score++;
        if (/[A-Z]/.test(pw)) score++;
        if (/[a-z]/.test(pw)) score++;
        if (/[0-9]/.test(pw)) score++;
        if (/[^A-Za-z0-9]/.test(pw)) score++;
        return score;
    };

    // Password validation function
    const validatePassword = (password) => {
        const requirements = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /\d/.test(password),
            special: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password),
        };

        // Update requirement checkmarks
        if (lengthCheck)
            lengthCheck.classList.toggle("valid", requirements.length);
        if (uppercaseCheck)
            uppercaseCheck.classList.toggle("valid", requirements.uppercase);
        if (lowercaseCheck)
            lowercaseCheck.classList.toggle("valid", requirements.lowercase);
        if (numberCheck)
            numberCheck.classList.toggle("valid", requirements.number);
        if (specialCheck)
            specialCheck.classList.toggle("valid", requirements.special);

        // Calculate strength
        const validCount = Object.values(requirements).filter(Boolean).length;
        let strength = "weak";
        let strengthLabel = "Weak";

        if (validCount === 5) {
            strength = "strong";
            strengthLabel = "Strong";
        } else if (validCount >= 4) {
            strength = "good";
            strengthLabel = "Good";
        } else if (validCount >= 3) {
            strength = "fair";
            strengthLabel = "Fair";
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
        if (passwordInput) {
            if (validCount === 5) {
                passwordInput.classList.remove("is-invalid");
                passwordInput.style.borderColor = "#10b981";
            } else if (password.length > 0) {
                passwordInput.style.borderColor =
                    validCount >= 3 ? "#f59e0b" : "#ef4444";
            } else {
                passwordInput.style.borderColor = "#e5e7eb";
            }
        }

        return validCount === 5;
    };

    // Password confirmation validation
    const validatePasswordMatch = () => {
        const password = passwordInput?.value || "";
        const confirmPassword = confInput?.value || "";

        if (!confirmPassword || !passwordMatch) {
            if (passwordMatch) {
                passwordMatch.textContent = "";
                passwordMatch.className = "password-match";
            }
            if (confInput) {
                confInput.classList.remove("is-valid", "is-invalid");
                confInput.style.borderColor = "#e5e7eb";
            }
            return false;
        }

        if (password === confirmPassword) {
            passwordMatch.textContent = "✓ Passwords match";
            passwordMatch.className = "password-match match";
            confInput.classList.remove("is-invalid");
            confInput.classList.add("is-valid");
            confInput.style.borderColor = "#10b981";
            return true;
        } else {
            passwordMatch.textContent = "✗ Passwords do not match";
            passwordMatch.className = "password-match no-match";
            confInput.classList.remove("is-valid");
            confInput.classList.add("is-invalid");
            confInput.style.borderColor = "#ef4444";
            return false;
        }
    };

    if (passwordInput) {
        passwordInput.addEventListener("input", () => {
            validatePassword(passwordInput.value);
            if (confInput?.value) {
                validatePasswordMatch();
            }
        });
    }

    if (confInput) {
        confInput.addEventListener("input", () => {
            validatePasswordMatch();
        });
    }

    // Original checkStrength kept for compatibility
    const checkStrength_old = (pw) => {
        let score = 0;
        if (pw.length >= 8) score++;
        if (/[A-Z]/.test(pw)) score++;
        if (/[a-z]/.test(pw)) score++;
        if (/[0-9]/.test(pw)) score++;
        if (/[^A-Za-z0-9]/.test(pw)) score++;
        return score;
    };

    // Form submission validation (updated)
    if (registerForm) {
        registerForm.addEventListener("submit", (e) => {
            const pw = passwordInput?.value || "";
            const conf = confInput?.value || "";
            const email = emailInput?.value.trim() || "";

            const isPasswordValid = validatePassword(pw);
            const isPasswordMatch = validatePasswordMatch();

            if (!isPasswordValid) {
                e.preventDefault();
                passwordInput.focus();
                return false;
            }

            if (!validateEmail(email)) {
                e.preventDefault();
                emailInput.focus();
                return false;
            }

            if (!isPasswordMatch) {
                e.preventDefault();
                confInput.focus();
                return false;
            }

            // Show loading animation
            Swal.fire({
                title: "Creating Account...",
                text: "Please wait while we process your registration.",
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            return true;
        });
    }

    /* -------------------------------------------
     * SweetAlert Session Success Message
     * ------------------------------------------- */
    if (window.swalMessage) {
        Swal.fire({
            icon: "success",
            title: window.swalTitle || "Success",
            text: window.swalMessage,
            timer: 2500,
            timerProgressBar: true,
            showConfirmButton: false,
        });
    }
});
