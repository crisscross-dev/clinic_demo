// ✅ Top import for Vite
import Swal from "sweetalert2";

document.addEventListener("DOMContentLoaded", () => {
    const $ = (sel) => document.querySelector(sel);
    const $$ = (sel) => document.querySelectorAll(sel);

    /* -------------------------------------------
     * SweetAlert Helper Functions
     * ------------------------------------------- */
    const showAlert = (icon, title, text, timer = 2500) => {
        Swal.fire({
            icon,
            title,
            text,
            timer,
            timerProgressBar: true,
            showConfirmButton: false,
        });
    };

    const showLoading = (msg = "Processing...") => {
        Swal.fire({
            title: msg,
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
        });
    };

    /* -------------------------------------------
     * Your other page logic here...
     * ------------------------------------------- */




    /* -------------------------------------------
     * Floating Labels
     * ------------------------------------------- */
    $$(".form-group").forEach((group) => {
        const input = group.querySelector(".form-control");
        if (!input) return;

        const update = () =>
            group.classList.toggle("filled", !!input.value.trim());
        input.addEventListener("focus", () => group.classList.add("focused"));
        input.addEventListener("blur", () => group.classList.remove("focused"));
        input.addEventListener("input", update);
        update();
    });

    /* -------------------------------------------
     * Password Toggle
     * ------------------------------------------- */
    const passwordInput = $("#password");
    const togglePassword = $("#togglePassword");
    if (passwordInput && togglePassword) {
        togglePassword.addEventListener("click", () => {
            const show = passwordInput.type === "password";
            passwordInput.type = show ? "text" : "password";
            const icon = togglePassword.querySelector("i");
            icon.classList.toggle("bi-eye-fill", !show);
            icon.classList.toggle("bi-eye-slash-fill", show);
        });
    }

    /* -------------------------------------------
     * Form Switching (Login <-> Forgot)
     * ------------------------------------------- */
    const loginContainer = $(".login-container");
    const loginForm = $(".login-form");
    const forgotForm = $(".forgot-form");
    const brandingContent = $(".branding-content");
    const forgotBranding = $(".forgot-branding");

    const switchToForgot = () => {
        loginContainer.classList.add("transitioning");
        brandingContent.classList.add("fade-out");
        loginForm.classList.add("slide-out-left");

        loginForm.addEventListener(
            "transitionend",
            () => {
                loginContainer.classList.add("flipped");
                forgotForm.classList.add("slide-in");
                forgotBranding.classList.add("fade-in");
                loginContainer.classList.remove("transitioning");
            },
            { once: true }
        );
    };

    const switchToLogin = () => {
        loginContainer.classList.add("transitioning");
        forgotBranding.classList.remove("fade-in");
        forgotForm.classList.remove("slide-in");

        forgotForm.addEventListener(
            "transitionend",
            () => {
                loginContainer.classList.remove("flipped");
                loginForm.classList.remove("slide-out-left");
                brandingContent.classList.remove("fade-out");
                loginContainer.classList.remove("transitioning");
            },
            { once: true }
        );
    };

    $("#forgot-password-link")?.addEventListener("click", (e) => {
        e.preventDefault();
        switchToForgot();
    });
    $("#back-to-login-link")?.addEventListener("click", (e) => {
        e.preventDefault();
        switchToLogin();
    });

    /* -------------------------------------------
     * Forgot Password AJAX
     * ------------------------------------------- */
    const forgotFormAjax = $(".forgot-form");
    const forgotEmail = $("#forgot-email");
    const forgotSubmit = $("#forgot-submit-btn");

    if (forgotFormAjax) {
        forgotFormAjax.addEventListener("submit", async (e) => {
            e.preventDefault();

            const email = forgotEmail?.value.trim() || "";
            const csrfToken = $("input[name='_token']").value;

            if (!email) {
                showAlert(
                    "warning",
                    "Missing Email",
                    "Please enter your username or email."
                );
                forgotEmail?.focus();
                return;
            }

            showLoading("Sending reset link...");

            const endpoints = [
                forgotFormAjax.action,
                "/student/password/email", // fallback route
            ];

            let success = false;

            for (const endpoint of endpoints) {
                try {
                    const formData = new FormData();
                    formData.append("email", email);
                    formData.append("_token", csrfToken);

                    const res = await fetch(endpoint, {
                        method: "POST",
                        headers: {
                            "X-Requested-With": "XMLHttpRequest",
                            "X-CSRF-TOKEN": csrfToken,
                            Accept: "application/json",
                        },
                        body: formData,
                    });

                    const data = await res.json();

                    if (res.ok) {
                        success = true;
                        Swal.close();
                        showAlert(
                            "success",
                            "Email Sent!",
                            data.message || "Check your inbox for reset link."
                        );
                        switchToLogin();
                        break;
                    } else if (data.type === "user_not_found") {
                        continue; // try next endpoint
                    } else {
                        Swal.close();
                        showAlert(
                            "error",
                            "Error",
                            data.message || "Please try again."
                        );
                        break;
                    }
                } catch (err) {
                    Swal.close();
                    showAlert(
                        "error",
                        "Network Error",
                        "Please check your connection."
                    );
                }
            }

            if (!success) Swal.close();
        });
    }

    /* -------------------------------------------
     * Login Field Feedback (just UI)
     * ------------------------------------------- */
    const loginIdentifier = $("#login_identifier");
    loginIdentifier?.addEventListener("input", (e) => {
        e.target.style.borderColor = e.target.value ? "#1e5799" : "#e5e7eb";
    });


        if (window.swalMessage) {
            showAlert(
                "success",
                window.swalTitle || "Success",
                window.swalMessage
            );
        }
});
