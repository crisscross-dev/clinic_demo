// Master List JavaScript functionality
document.addEventListener("DOMContentLoaded", function () {
    initializeDropdown();
    initializeSearch();
    initializeDepartmentFilter();
    initializeActionsDropdown();
});

// Dropdown functionality
function toggleDropdown() {
    const dropdown = document.getElementById("userDropdown");
    dropdown.style.display =
        dropdown.style.display === "block" ? "none" : "block";
}

function initializeDropdown() {
    // Close dropdown when clicking outside
    document.addEventListener("click", function (event) {
        const dropdown = document.getElementById("userDropdown");
        const button = event.target.closest(".dropdown-wrapper button");

        if (!button && dropdown && dropdown.style.display === "block") {
            dropdown.style.display = "none";
        }
    });
}

// Department filtering functionality
function initializeDepartmentFilter() {
    const departmentFilters = document.querySelectorAll(".department-filter");

    departmentFilters.forEach((filter) => {
        filter.addEventListener("click", function (e) {
            e.preventDefault();

            // Remove active class from all filters
            departmentFilters.forEach((f) => f.classList.remove("active"));

            // Add active class to clicked filter
            this.classList.add("active");

            // Get selected department
            const selectedDept = this.getAttribute("data-department");
            const displayText = selectedDept || "All Departments";

            // Update button text
            document.getElementById("selectedDepartment").textContent =
                displayText;

            // Filter table rows
            filterByDepartment(selectedDept);

            // Close dropdown
            document.getElementById("userDropdown").style.display = "none";
        });
    });
}

function filterByDepartment(department) {
    const rows = document.querySelectorAll(".patient-table tbody tr");
    let visibleCount = 0;

    rows.forEach((row) => {
        // Skip empty state row
        if (row.cells.length === 1) return;

        const departmentCell = row.cells[2]; // Department is 3rd column (index 2)
        const rowDepartment = departmentCell.textContent.trim();

        // Show row if no filter or department matches
        const shouldShow =
            !department ||
            rowDepartment === department ||
            rowDepartment === "—";

        if (shouldShow && (!department || rowDepartment !== "—")) {
            row.style.display = "";
            visibleCount++;
        } else if (!department && rowDepartment === "—") {
            row.style.display = "";
            visibleCount++;
        } else {
            row.style.display = "none";
        }
    });

    // Update title with department and count
    updateTitleWithCount(department, visibleCount);

    // Update empty state if needed
    updateEmptyStateForFilter(department, visibleCount);
}

// Search functionality
function initializeSearch() {
    const searchInput = document.getElementById("searchInput");
    if (!searchInput) return;

    searchInput.addEventListener("input", function (e) {
        const searchTerm = e.target.value.toLowerCase().trim();
        const rows = document.querySelectorAll(".patient-table tbody tr");

        rows.forEach((row) => {
            // Skip empty state row
            if (row.cells.length === 1) return;

            const text = row.textContent.toLowerCase();
            const shouldShow = searchTerm === "" || text.includes(searchTerm);
            row.style.display = shouldShow ? "" : "none";
        });

        // Show/hide empty state
        updateEmptyState(searchTerm);
    });
}

function updateEmptyState(searchTerm) {
    const visibleRows = document.querySelectorAll(
        '.patient-table tbody tr[style=""], .patient-table tbody tr:not([style])'
    );
    const emptyRow = document.querySelector(
        '.patient-table tbody tr td[colspan="7"]'
    );

    if (visibleRows.length === 0 && searchTerm && emptyRow) {
        emptyRow.innerHTML = `
            <div class="text-muted">
                <i class="fas fa-search fa-2x mb-2"></i>
                <p>No patients found matching "${searchTerm}"</p>
            </div>
        `;
    }
}

function updateTitleWithCount(department, count) {
    const departmentLabel = document.getElementById("departmentLabel");
    const patientCount = document.getElementById("patientCount");

    if (departmentLabel && patientCount) {
        // Update department label
        if (department) {
            departmentLabel.textContent = `${department}`;
        } else {
            departmentLabel.textContent = "All";
        }

        // Update count
        patientCount.textContent = `${count}`;
    }
}

function updateEmptyStateForFilter(department, visibleCount) {
    const emptyRow = document.querySelector(
        '.patient-table tbody tr td[colspan="7"]'
    );

    if (visibleCount === 0 && department && emptyRow) {
        emptyRow.innerHTML = `
            <div class="text-muted">
                <i class="fas fa-filter fa-2x mb-2"></i>
                <p>No patients found in ${department} department</p>
            </div>
        `;
        emptyRow.parentElement.style.display = "";
    } else if (visibleCount > 0 && emptyRow) {
        emptyRow.parentElement.style.display = "none";
    }
}

// Actions gear dropdown
function initializeActionsDropdown() {
    document.addEventListener("click", function (e) {
        const toggle = e.target.closest(".actions-toggle");
        const allMenus = document.querySelectorAll(".actions-menu");

        if (toggle) {
            const container = toggle.closest(".actions-dropdown");
            const menu = container.querySelector(".actions-menu");
            const isOpen = menu && menu.style.display === "block";

            // Close all
            allMenus.forEach((m) => {
                m.style.display = "none";
                m.style.top = "";
                m.style.bottom = "";
            });
            document
                .querySelectorAll('.actions-toggle[aria-expanded="true"]')
                .forEach((btn) => btn.setAttribute("aria-expanded", "false"));

            // Toggle current
            if (menu && !isOpen) {
                // Reset positioning
                menu.style.position = "absolute";
                menu.style.left = "auto";
                menu.style.right = "0";

                // Calculate available space
                const rect = toggle.getBoundingClientRect();
                const viewportHeight = window.innerHeight;
                const spaceBelow = viewportHeight - rect.bottom;
                const spaceAbove = rect.top;

                if (spaceBelow > 200 || spaceBelow > spaceAbove) {
                    // enough space below → show below
                    menu.style.top = "100%";
                    menu.style.bottom = "auto";
                } else {
                    // not enough space below → show above
                    menu.style.top = "auto";
                    menu.style.bottom = "100%";
                }

                menu.style.display = "block";
                toggle.setAttribute("aria-expanded", "true");
            }
            return;
        }

        // Click outside closes menus
        if (!e.target.closest(".actions-dropdown")) {
            allMenus.forEach((m) => (m.style.display = "none"));
            document
                .querySelectorAll('.actions-toggle[aria-expanded="true"]')
                .forEach((btn) => btn.setAttribute("aria-expanded", "false"));
        }

        // Click on any action inside menu closes it
        const actionItem = e.target.closest(
            ".actions-menu a, .actions-menu button"
        );
        if (actionItem) {
            const menu = actionItem.closest(".actions-menu");
            const container = actionItem.closest(".actions-dropdown");
            const toggleBtn = container
                ? container.querySelector(".actions-toggle")
                : null;
            if (menu) menu.style.display = "none";
            if (toggleBtn) toggleBtn.setAttribute("aria-expanded", "false");
        }
    });
}
