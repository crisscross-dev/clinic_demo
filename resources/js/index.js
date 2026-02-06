// Patient Portal Specific JavaScript
function showAppointmentForm() {
    const form = document.getElementById("appointmentForm");
    const ctaSection = document.querySelector(".cta-section");

    // Hide CTA section with fade out
    ctaSection.style.transition = "all 0.5s ease";
    ctaSection.style.opacity = "0";
    ctaSection.style.transform = "translateY(-20px)";

    // Show appointment form after CTA fades out
    setTimeout(() => {
        ctaSection.style.display = "none";
        form.classList.add("show");

        // Smooth scroll to form
        form.scrollIntoView({
            behavior: "smooth",
            block: "start",
        });

        // Focus on first input
        setTimeout(() => {
            const firstInput = form.querySelector('input[type="text"]');
            if (firstInput) firstInput.focus();
        }, 600);
    }, 500);
}

function scrollToServices() {
    const servicesSection = document.getElementById("services");
    servicesSection.scrollIntoView({
        behavior: "smooth",
        block: "start",
    });
}

// Initialize patient portal interactions
document.addEventListener("DOMContentLoaded", () => {
    // Auto-show form if session says so
    if (window.showFormOnLoad) {
        showAppointmentForm();
    }

    // Attach button click listener
    const fillFormButton = document.querySelector(".fill-form");
    if (fillFormButton) {
        fillFormButton.addEventListener("click", showAppointmentForm);
    }

    // Optionally attach other buttons
    const learnServicesButton = document.querySelector(
        ".btn-cta.btn-secondary-cta"
    );
    if (learnServicesButton) {
        learnServicesButton.addEventListener("click", scrollToServices);
    }

    // Initialize health form functions if present
    if (typeof initializeHealthForm === "function") {
        initializeHealthForm();
    }
});
