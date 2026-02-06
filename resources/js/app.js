// Import Bootstrap 5 CSS and JS
import * as bootstrap from "bootstrap";

// Expose Bootstrap to window so Blade inline scripts can use window.bootstrap
window.bootstrap = bootstrap;

// Import SweetAlert2 (JS + CSS)
import "sweetalert2/dist/sweetalert2.min.css";
import Swal from "sweetalert2";
import "./shared/alert_message.js";
// Ensure bootstrap icons CSS is included in the build output
import "bootstrap-icons/font/bootstrap-icons.css";

window.Swal = Swal;

// Import SignaturePad and expose globally for Blade template fallback
import SignaturePad from "signature_pad";
window.SignaturePad = SignaturePad;

// Import your custom scripts
import "./shared/health-form-signature.js";

import Chart from "chart.js/auto";
// Example: draw a simple chart
const ctx = document.getElementById("myChart");
if (ctx) {
    new Chart(ctx, {
        type: "bar",
        data: {
            labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
            datasets: [
                {
                    label: "# of Votes",
                    data: [12, 19, 3, 5, 2, 3],
                },
            ],
        },
    });
}

import Litepicker from "litepicker";
import "litepicker/dist/css/litepicker.css";

// Example: attach to an input
const input = document.getElementById("datepicker");

if (input) {
    new Litepicker({
        element: input,
        singleMode: true,
    });
}

// Expose Chart and Litepicker globally so other modules can reuse them
window.Chart = Chart;
window.Litepicker = Litepicker;
