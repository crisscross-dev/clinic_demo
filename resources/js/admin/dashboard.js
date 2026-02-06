/**
 * ==============================
 * DASHBOARD UTILITIES
 * ==============================
 */
// Use shared Chart and Litepicker exposed by `resources/js/app.js`
const Chart = window.Chart;
const Litepicker = window.Litepicker;
import { setupRangePicker } from "../shared/litepicker-setup";

// Fallback: try dynamic import if not yet available (useful during dev HMR)
if (typeof Chart === "undefined" || typeof Litepicker === "undefined") {
    // dynamic import won't block but ensures values are available
    (async () => {
        const [{ default: _Chart }, { default: _Litepicker }] =
            await Promise.all([import("chart.js/auto"), import("litepicker")]);
        window.Chart = window.Chart || _Chart;
        window.Litepicker = window.Litepicker || _Litepicker;
    })();
}

const DashboardUtils = (() => {
    /** Format date as YYYY-MM-DD */
    function formatDate(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, "0");
        const d = String(date.getDate()).padStart(2, "0");
        return `${y}-${m}-${d}`;
    }

    /** Format date as "MMM D, YYYY" (e.g., Aug 22, 2025) */
    function formatDisplayDate(date) {
        return new Date(date).toLocaleDateString("en-US", {
            month: "short",
            day: "numeric",
            year: "numeric",
        });
    }

    /** Destroy chart safely */
    function destroyChart(chart) {
        if (chart) chart.destroy();
        return null;
    }

    /** Get default date range (e.g., last 7 days) */
    function defaultRange(days = 7) {
        const today = new Date();
        const start = new Date();
        start.setDate(today.getDate() - (days - 1));
        return { start, end: today };
    }

    // NOTE: Litepicker setup is centralized in `resources/js/shared/litepicker-setup.js`

    return {
        formatDate,
        formatDisplayDate,
        destroyChart,
        defaultRange,
        // setupPicker removed — use shared setupRangePicker instead
    };
})();

/**
 * ==============================
 * CHART RENDERERS
 * ==============================
 */
function makeLineChart(canvas, labels, data, label = "Consultations") {
    return new Chart(canvas, {
        type: "line",
        data: {
            labels,
            datasets: [
                {
                    label,
                    data,
                    borderColor: "#1e5799",
                    backgroundColor: "rgba(32,124,202,.12)",
                    tension: 0.3,
                    fill: true,
                    pointRadius: 3,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            plugins: { legend: { display: false } },
        },
    });
}

function makeBarChart(canvas, labels, data, label = "Used") {
    return new Chart(canvas, {
        type: "bar",
        data: {
            labels,
            datasets: [
                {
                    label,
                    data,
                    backgroundColor: "#d9534fAA",
                    borderColor: "#d9534f",
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: "y",
            scales: {
                x: { beginAtZero: true, ticks: { precision: 0 } },
                y: {},
            },
            plugins: { legend: { display: false } },
        },
    });
}

/**
 * ==============================
 * CONSULTATIONS CHART
 * ==============================
 */
(function () {
    const canvas = document.getElementById("registrations7d");
    const startEl = document.getElementById("consult-start");
    const endEl = document.getElementById("consult-end");
    const labelEl = document.getElementById("selected-date-text");
    const btn = document.getElementById("date-btn");
    if (!canvas || !startEl || !endEl || !btn) return;

    let chart;

    async function fetchSeries(start, end) {
        const params = new URLSearchParams({ start, end });
        const res = await fetch(
            `${window.dashboardRoutes.consultationsSeries}?${params}`,
            {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            }
        );
        return res.ok ? res.json() : {};
    }

    async function apply(start, end) {
        const series = await fetchSeries(start, end);
        const labels = Object.keys(series);
        const data = Object.values(series);
        chart = DashboardUtils.destroyChart(chart);
        chart = makeLineChart(canvas, labels, data, "Consultations");
    }

    // Use shared Litepicker setup (default to monthly)
    setupRangePicker({
        button: btn,
        startInput: startEl,
        endInput: endEl,
        labelElement: labelEl,
        onApply: apply,
        days: 31,
        openAtToday: true,
    });
    // Populate chart with default range (setupRangePicker sets inputs/label)
    {
        const { start, end } = DashboardUtils.defaultRange(31);
        const s = DashboardUtils.formatDate(start);
        const e = DashboardUtils.formatDate(end);
        apply(s, e);
    }
})();

/**
 * ==============================
 * INVENTORY CHART
 * ==============================
 */
(function () {
    const canvas = document.getElementById("chart-inventory");
    const startEl = document.getElementById("inv-start");
    const endEl = document.getElementById("inv-end");
    const labelEl = document.getElementById("inv-date-range");
    const btn = document.getElementById("inv-date-btn");
    if (!canvas || !startEl || !endEl || !btn || !labelEl) return;

    let chart;

    async function fetchSeries(start, end) {
        const params = new URLSearchParams({ start, end });
        const res = await fetch(
            `${window.dashboardRoutes.inventorySeries}?${params}`,
            {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            }
        );
        return res.ok ? res.json() : { labels: [], used: [] };
    }

    async function apply(start, end) {
        const series = await fetchSeries(start, end);
        let labels = series.labels || [];
        let used = (series.used || []).map(Number);

        // Keep only nonzero & top 15 items
        const filtered = labels
            .map((l, i) => ({ l, v: used[i] }))
            .filter((x) => x.v > 0)
            .sort((a, b) => b.v - a.v)
            .slice(0, 15);

        labels = filtered.map((x) => x.l);
        used = filtered.map((x) => x.v);

        // Adjust chart height dynamically
        canvas.height = Math.max(120, labels.length * 26);

        chart = DashboardUtils.destroyChart(chart);
        chart = makeBarChart(canvas, labels, used, "Inventory Used");
    }

    // Use shared Litepicker setup
    setupRangePicker({
        button: btn,
        startInput: startEl,
        endInput: endEl,
        labelElement: labelEl,
        onApply: apply,
        days: 31,
        openAtToday: true,
    });
    // Populate chart with default range (setupRangePicker sets inputs/label)
    {
        const { start, end } = DashboardUtils.defaultRange(31);
        const s = DashboardUtils.formatDate(start);
        const e = DashboardUtils.formatDate(end);
        apply(s, e);
    }
})();

// Removed duplicate Litepicker setups for overall and admin to avoid double events

/**
 * ==============================
 * INVENTORY USAGE (TABLE VIEW)
 * ==============================
 */
(function () {
    const buttons = document.querySelectorAll(".usage-range");
    const rows = Array.from(document.querySelectorAll(".usage-row"));
    if (!buttons.length || !rows.length) return;

    function applyRange(key) {
        const k = ["7d", "30d"].includes(key) ? key : "today";

        // Update table cell values
        rows.forEach((row) => {
            const val = parseInt(row.dataset[k] || "0", 10);
            const cell = row.querySelector(".usage-cell");
            if (cell) cell.textContent = val;
        });

        // Sort rows by usage
        const sorted = rows
            .map((r) => ({
                row: r,
                val: parseInt(
                    r.querySelector(".usage-cell")?.textContent || "0",
                    10
                ),
            }))
            .sort((a, b) => b.val - a.val)
            .map((x) => x.row);

        const tbody = document.querySelector("table tbody");
        if (!tbody) return;

        // Display top 10 only
        sorted.forEach((r, idx) => {
            tbody.appendChild(r);
            r.style.display = idx < 10 ? "" : "none";
        });
    }

    buttons.forEach((btn) =>
        btn.addEventListener("click", () => {
            buttons.forEach((b) => b.classList.toggle("active", b === btn));
            applyRange(btn.dataset.range);
        })
    );

    // Default view
    applyRange("today");
})();

function updateAdminConsultationsChart(data) {
    const chartCanvas = document.getElementById("registrations7d-admin");
    const noDataDiv = document.getElementById("no-consultations-admin");
    if (!data || !data.length || data.every((v) => v === 0)) {
        chartCanvas.style.display = "none";
        noDataDiv.style.display = "block";
    } else {
        chartCanvas.style.display = "block";
        noDataDiv.style.display = "none";
        // ...update chart with data...
    }
}
// Example usage after fetching data:
// updateAdminConsultationsChart([/* array of values for chart */]);

(function () {
    const canvas = document.getElementById("registrations7d-admin");
    const startEl = document.getElementById("consult-start-admin");
    const endEl = document.getElementById("consult-end-admin");
    const labelEl = document.getElementById("selected-date-text-admin");
    const btn = document.getElementById("date-btn-admin");
    const noDataDiv = document.getElementById("no-consultations-admin");
    if (!canvas || !startEl || !endEl || !btn || !labelEl || !noDataDiv) return;

    let chart;

    // Get admin_id from a global JS variable or a hidden input
    const adminId =
        window.adminId || document.getElementById("admin-id")?.value;

    async function fetchSeries(start, end) {
        const params = new URLSearchParams({ start, end });
        if (adminId) params.append("admin_id", adminId);
        // Backend should filter by admin_id
        const res = await fetch(
            `${window.dashboardRoutes.consultationsSeries}?${params}`,
            {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            }
        );
        return res.ok ? res.json() : {};
    }

    async function apply(start, end) {
        const series = await fetchSeries(start, end);
        const labels = Object.keys(series);
        const data = Object.values(series);
        chart = DashboardUtils.destroyChart(chart);
        if (!data.length || data.every((v) => v === 0)) {
            canvas.style.display = "none";
            noDataDiv.style.display = "block";
        } else {
            canvas.style.display = "block";
            noDataDiv.style.display = "none";
            chart = makeLineChart(canvas, labels, data, "Your Consultations");
        }
    }

    setupRangePicker({
        button: btn,
        startInput: startEl,
        endInput: endEl,
        labelElement: labelEl,
        onApply: apply,
        days: 31,
        openAtToday: true,
    });
    // Populate admin chart with default range (setupRangePicker sets inputs/label)
    {
        const { start, end } = DashboardUtils.defaultRange(31);
        const s = DashboardUtils.formatDate(start);
        const e = DashboardUtils.formatDate(end);
        apply(s, e);
    }
})();
