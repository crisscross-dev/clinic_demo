import { initializeHealthForm as initSignature } from "../shared/health-form-signature.js";

function initializeHealthForm() {
    // Add checkbox interaction effects
    const checkboxLabels = document.querySelectorAll(
        ".checkbox-label, .consent-label"
    );
    checkboxLabels.forEach((label) => {
        // Prevent duplicate initialization
        if (label.dataset.checkboxInitialized === "true") return;

        const checkbox = label.querySelector('input[type="checkbox"]');

        // Skip initialization for disabled checkboxes
        if (label.classList.contains("disabled") || checkbox.disabled) {
            return;
        }

        // Make entire label clickable
        label.addEventListener("click", function (e) {
            if (e.target === checkbox) return; // ignore direct clicks

            // Toggle checkbox state
            checkbox.checked = !checkbox.checked;
            checkbox.dispatchEvent(new Event("change", { bubbles: true }));

            e.preventDefault();
            e.stopPropagation();
        });

        // Handle direct checkbox changes
        checkbox.addEventListener("change", function () {
            updateCheckboxVisual(label, checkbox);
        });

        // Initialize visual state
        updateCheckboxVisual(label, checkbox);

        // Hover effect
        label.addEventListener("mouseenter", function () {
            if (!checkbox.checked) {
                label.style.borderColor = "#3b82f6";
                label.style.backgroundColor = "#f8fafc";
            }
        });

        label.addEventListener("mouseleave", function () {
            if (!checkbox.checked) {
                if (label.classList.contains("no-consent")) {
                    label.style.borderColor = "#e5e7eb"; // keep neutral until checked
                    label.style.backgroundColor = "white";
                } else {
                    label.style.borderColor = "#e5e7eb";
                    label.style.backgroundColor = "white";
                }
            }
        });

        // Mark as initialized
        label.dataset.checkboxInitialized = "true";
    });

    // ================================
    // Consent checkbox exclusivity
    // ================================
    const consentCheckboxes = document.querySelectorAll(
        'input[name="consent[]"]:not([disabled])' // Only target enabled checkboxes
    );
    const noConsentCheckbox = Array.from(consentCheckboxes).find((cb) =>
        cb.value.includes("I do not consent")
    );

    consentCheckboxes.forEach((cb) => {
        cb.addEventListener("change", () => {
            if (cb === noConsentCheckbox && cb.checked) {
                // If "No consent" is checked, uncheck all others
                consentCheckboxes.forEach((other) => {
                    if (other !== noConsentCheckbox) {
                        other.checked = false;
                        updateCheckboxVisual(
                            other.closest(".consent-label"),
                            other
                        );
                    }
                });
            } else if (cb !== noConsentCheckbox && cb.checked) {
                // If any other is checked, uncheck "No consent"
                if (noConsentCheckbox) {
                    noConsentCheckbox.checked = false;
                    updateCheckboxVisual(
                        noConsentCheckbox.closest(".consent-label"),
                        noConsentCheckbox
                    );
                }
            }

            // Always refresh the one that triggered the change
            updateCheckboxVisual(cb.closest(".consent-label"), cb);
        });
    });
}

function updateCheckboxVisual(label, checkbox) {
    // Skip visual updates for disabled checkboxes
    if (label.classList.contains("disabled") || checkbox.disabled) {
        return;
    }

    if (checkbox.checked) {
        label.classList.add("checked");

        if (label.classList.contains("no-consent")) {
            // Orange border + background for no-consent
            label.style.borderColor = "#f59e0b";
            label.style.backgroundColor = "#fef3c7";
        } else {
            // Blue border + background for others
            label.style.borderColor = "#3b82f6";
            label.style.backgroundColor = "#eff6ff";
        }
    } else {
        label.classList.remove("checked");
        label.style.borderColor = "#e5e7eb";
        label.style.backgroundColor = "white";
    }
}

// Enhanced initialization function for modal contexts
function initializeFormWhenReady() {
    const form = document.querySelector(".health-form");
    // Wait for both form and at least one consent checkbox to exist
    const consentCheckbox = document.querySelector('input[name="consent[]"]');

    if (form && consentCheckbox) {
        console.log(
            "[health-form] Form and consent checkbox found, initializing..."
        );

        form.addEventListener("keydown", function (event) {
            if (event.key === "Enter" && event.target.tagName !== "TEXTAREA") {
                event.preventDefault();
            }
        });

        // Add real-time validation feedback
        addValidationFeedback();

        // Initialize checkboxes & exclusivity
        initializeHealthForm();

        // Initialize signature pad
        try {
            console.log("[health-form] calling initSignature");
            if (typeof initSignature === "function") {
                initSignature();
            }
        } catch (err) {
            console.error("Failed to initialize signature module:", err);
        }
    } else {
        // Retry after a short delay for modal contexts, up to 20 times (2s max)
        if (!window._healthFormInitTries) window._healthFormInitTries = 0;
        window._healthFormInitTries++;
        if (window._healthFormInitTries <= 20) {
            console.warn(
                "[health-form] Waiting for form/checkbox, retry " +
                    window._healthFormInitTries
            );
            setTimeout(initializeFormWhenReady, 100);
        } else {
            console.error(
                "[health-form] Consent checkboxes not found after multiple retries."
            );
        }
    }
}

function addValidationFeedback() {
    const requiredFields = document.querySelectorAll(
        "input[required], select[required]"
    );

    requiredFields.forEach((field) => {
        // Add event listeners for real-time validation
        field.addEventListener("blur", function () {
            validateField(this);
        });

        field.addEventListener("input", function () {
            if (this.classList.contains("is-invalid")) {
                validateField(this);
            }
        });

        field.addEventListener("change", function () {
            if (this.classList.contains("is-invalid")) {
                validateField(this);
            }
        });
    });
}

function validateField(field) {
    const value = field.value.trim();
    const isValid = value !== "";

    if (isValid) {
        field.classList.remove("is-invalid");
        field.classList.add("is-valid");
        const feedback = field.parentNode.querySelector(".invalid-feedback");
        if (feedback) feedback.remove();
    } else {
        field.classList.remove("is-valid");
        field.classList.add("is-invalid");

        // Add invalid feedback message if it doesn't exist
        let feedback = field.parentNode.querySelector(".invalid-feedback");
        if (!feedback) {
            feedback = document.createElement("div");
            feedback.className = "invalid-feedback";
            feedback.textContent = "This field is required.";
            field.parentNode.appendChild(feedback);
        }
    }
}

// =============================
// DOM Ready and Modal Support
// =============================
document.addEventListener("DOMContentLoaded", function () {
    console.log("[health-form] DOM ready, checking for form...");

    // Check if we're in a modal context
    const modal = document.getElementById("healthFormModal");
    if (modal) {
        console.log("[health-form] Modal context detected");

        // Initialize when modal is shown
        modal.addEventListener("shown.bs.modal", function () {
            console.log("[health-form] Modal shown, initializing form");
            setTimeout(initializeFormWhenReady, 50); // Small delay for modal content
        });

        // If modal is already open, initialize immediately
        if (modal.classList.contains("show")) {
            initializeFormWhenReady();
        }
    } else {
        // Regular page context
        initializeFormWhenReady();
    }
});

// Export for external use if needed
window.initializeHealthForm = initializeFormWhenReady;
