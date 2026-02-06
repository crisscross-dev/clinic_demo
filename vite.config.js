import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/css/index.css",
                "resources/css/sidebar.css",
                "resources/css/login.css",
                "resources/css/login_register.css",

                "resources/css/shared/base.css",
                "resources/css/shared/bootstrap.min.css",
                "resources/css/shared/buttons.css",
                "resources/css/shared/components.css",
                "resources/css/shared/pagination.css",

                "resources/css/patients/patient-index.css",
                "resources/css/patients/edit.css",
                "resources/css/patients/show.css",
                "resources/css/patients/consultations/index.css",

                "resources/css/student/dashboard.css",

                "resources/css/partials/health-form.css",

                "resources/css/inventory/index.css",
                "resources/css/inventory/report_modal.css",

                "resources/css/admin/dashboard.css",
                "resources/css/admin/pending.css",
                "resources/css/admin/login.css",
                "resources/css/admin/consent_request.css",
                "resources/css/admin/account_list.css",

                "resources/js/app.js",
                "resources/js/index.js",
                "resources/js/login.js",
                "resources/js/login_register.js",

                "resources/js/admin/dashboard.js",

                "resources/js/inventory/index.js",
                "resources/js/inventory/report_modal.js",

                "resources/js/partials/health-form.js",

                "resources/js/patients/index_patients.js",
                "resources/js/patients/show.js",

                "resources/js/student/dashboard_student.js",

                "resources/js/shared/checkbox.js",
                "resources/js/shared/alert_message.js",
                "resources/js/shared/bootstrap.bundle.min.js",
                "resources/js/shared/health-form-signature.js",
                "resources/js/shared/litepicker-setup.js",
                "resources/js/shared/sidebar.js",
                "resources/js/shared/signature_pad.umd.min.js",
                "resources/js/shared/sweetalert2.all.min.js",
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: "0.0.0.0", // allow access from both localhost and LAN devices
        port: 5173,
        hmr: {
            host: "192.168.1.9", // your PC’s LAN IP (for phone/tablet access)
        },
    },
    build: {
        minify: false,
    },
    // ⚠️ DO NOT set base: "./" — Laravel plugin handles paths automatically
});
