import { setupRangePicker } from "../shared/litepicker-setup";

window.addEventListener("load", () => {
    setupRangePicker({
        button: document.getElementById("dateRangeBtn"),
        startInput: document.getElementById("startDate"),
        endInput: document.getElementById("endDate"),
        preset: false,
        openAtToday: true,
    });
});
