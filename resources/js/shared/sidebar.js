document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("toggle-btn");
    const sidebar = document.getElementById("sidebar");
    const body = document.body;

    // If toggle button is not yet present, bail gracefully to avoid JS errors.
    if (!toggleBtn || !sidebar) return;

    // Initialize aria state for accessibility
    toggleBtn.setAttribute("aria-controls", "sidebar");
    toggleBtn.setAttribute(
        "aria-expanded",
        String(!sidebar.classList.contains("collapsed"))
    );

    // Create overlay backdrop for small screens and insert before sidebar
    const overlay = document.createElement("div");
    overlay.className = "sidebar-overlay";
    document.body.insertBefore(overlay, sidebar);
    overlay.addEventListener("click", () => {
        // close sidebar when overlay clicked
        if (
            !sidebar.classList.contains("collapsed") &&
            window.innerWidth <= 768
        ) {
            sidebar.classList.add("collapsed");
            body.classList.add("collapsed");
            overlay.classList.remove("visible");
            toggleBtn.textContent = "☰";
            toggleBtn.setAttribute("aria-expanded", "false");
        }
    });

    function handleSidebarByScreen() {
        if (window.innerWidth <= 768) {
            sidebar.classList.add("collapsed");
            body.classList.add("collapsed");
            toggleBtn.textContent = "☰";
            toggleBtn.setAttribute("aria-expanded", "false");
            // ensure overlay hidden by default on small screens
            overlay.classList.remove("visible");
        } else {
            sidebar.classList.remove("collapsed");
            body.classList.remove("collapsed");
            toggleBtn.textContent = "✖";
            toggleBtn.setAttribute("aria-expanded", "true");
            // hide overlay on larger screens
            overlay.classList.remove("visible");
        }
    }

    handleSidebarByScreen();
    window.addEventListener("resize", handleSidebarByScreen);

    toggleBtn.addEventListener("click", () => {
        const isCollapsed = sidebar.classList.toggle("collapsed");
        body.classList.toggle("collapsed");
        toggleBtn.textContent = isCollapsed ? "☰" : "✖";
        toggleBtn.setAttribute("aria-expanded", String(!isCollapsed));

        // On small screens, show/hide overlay
        if (window.innerWidth <= 768) {
            if (!isCollapsed) overlay.classList.add("visible");
            else overlay.classList.remove("visible");
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const toggles = document.querySelectorAll(".submenu-toggle");

    // Helper to close all open submenus (optionally except one)
    function closeAllSubmenus(except = null) {
        document.querySelectorAll(".has-submenu.open").forEach((item) => {
            if (except && item === except) return;
            item.classList.remove("open");
        });
    }

    toggles.forEach((toggle) => {
        toggle.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();

            const parent = this.closest(".has-submenu");
            if (!parent) return;

            const wasOpen = parent.classList.contains("open");

            // Close others then toggle this one
            closeAllSubmenus(parent);
            parent.classList.toggle("open", !wasOpen);
        });
    });

    // Close submenus when clicking outside the sidebar
    document.addEventListener("click", function (e) {
        // If click is inside an open submenu or its toggle, do nothing
        const clickedInside =
            e.target.closest && e.target.closest(".has-submenu");
        if (!clickedInside) {
            closeAllSubmenus();
        }
    });

    // Close submenus on Escape key
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape" || e.key === "Esc") {
            closeAllSubmenus();
        }
    });
});
