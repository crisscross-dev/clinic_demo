// Health Form Signature Manager
// Handles signature pad initialization, preview canvas, and modal interactions
import SignaturePad from "signature_pad";
import Swal from "sweetalert2";

export function initializeHealthForm() {
    try {
        // DOM Elements
        const elements = {
            form: document.querySelector("form.health-form"),
            modalCanvas: document.getElementById("signature-pad"),
            previewCanvas: document.getElementById("signature-preview-canvas"),
            clearBtn: document.getElementById("clear-signature"),
            hiddenInput: document.getElementById("signature-input"),
            modal: document.getElementById("signatureModal"),
            openBtn: document.getElementById("openSignatureModalBtn"),
            saveBtn: document.getElementById("save-signature"),
            consentInput: document.getElementById("consent_by"),
        };

        // Early exit if required elements are missing
        if (
            !elements.form ||
            !elements.modalCanvas ||
            !elements.previewCanvas ||
            !elements.hiddenInput
        ) {
            console.log("[signature] Required elements not found - skipping");
            return;
        }

        console.log("[signature] Form element found:", elements.form);
        console.log("[signature] Form ID:", elements.form.id);
        console.log("[signature] Form class:", elements.form.className);
        console.log("[signature] Hidden input found:", elements.hiddenInput);
        console.log("[signature] Hidden input ID:", elements.hiddenInput.id);
        console.log(
            "[signature] Hidden input name:",
            elements.hiddenInput.name
        );
        console.log(
            "[signature] Hidden input is inside form:",
            elements.form.contains(elements.hiddenInput)
        );

        // State
        let signaturePad = null;
        let modalInstance = null;
        let hasActualSignature = false; // Track if user has drawn/saved a real signature

        // Constants
        const PREVIEW_HEIGHT = 180;
        const NAME_AREA_HEIGHT = 60; // Reserved space for consent name beneath signature
        const MODAL_HEIGHT = 300; // Increased height for better drawing space

        // ==================== Canvas Setup ====================

        function setupCanvas(canvas, height) {
            const rect = canvas.getBoundingClientRect();
            const ratio = window.devicePixelRatio || 1;
            canvas.width = rect.width * ratio;
            canvas.height = height * ratio;
            const ctx = canvas.getContext("2d");
            ctx.scale(ratio, ratio);
            return ctx;
        }

        // Initialize preview canvas once
        const previewCtx = setupCanvas(elements.previewCanvas, PREVIEW_HEIGHT);

        // ==================== Placeholder Display ====================

        function showPlaceholder() {
            const ctx = elements.previewCanvas.getContext("2d");
            const ratio = window.devicePixelRatio || 1;
            const width = elements.previewCanvas.width / ratio;
            const height = elements.previewCanvas.height / ratio;

            // Clear canvas
            ctx.clearRect(0, 0, width, height);

            // Fill with subtle background so export is not transparent
            ctx.fillStyle = "#fff";
            ctx.fillRect(0, 0, width, height);

            // Show placeholder message
            ctx.font = "14px Arial";
            ctx.fillStyle = "#999";
            ctx.textAlign = "center";
            ctx.textBaseline = "middle";
            ctx.fillText(
                "Click 'Add Signature' to draw your signature",
                width / 2,
                height / 2
            );
        }

        function renderCompositeSignature(sourceCanvas, consentName) {
            if (!sourceCanvas) return null;

            const ratio = window.devicePixelRatio || 1;
            const canvasWidth = elements.previewCanvas.width / ratio;
            const canvasHeight = elements.previewCanvas.height / ratio;
            const signatureHeight = Math.max(
                0,
                canvasHeight - NAME_AREA_HEIGHT
            );

            previewCtx.save();
            previewCtx.clearRect(0, 0, canvasWidth, canvasHeight);

            // White background to avoid transparency when exporting
            previewCtx.fillStyle = "#fff";
            previewCtx.fillRect(0, 0, canvasWidth, canvasHeight);

            // Draw the signature strokes scaled to fit preview area
            previewCtx.drawImage(
                sourceCanvas,
                0,
                0,
                canvasWidth,
                signatureHeight
            );

            if (consentName) {
                previewCtx.strokeStyle = "#666";
                previewCtx.lineWidth = 1;
                previewCtx.beginPath();
                previewCtx.moveTo(canvasWidth * 0.1, signatureHeight + 15);
                previewCtx.lineTo(canvasWidth * 0.9, signatureHeight + 15);
                previewCtx.stroke();

                previewCtx.textAlign = "center";
                previewCtx.textBaseline = "top";

                previewCtx.font = "14px Arial";
                previewCtx.fillStyle = "#333";
                previewCtx.fillText(
                    consentName,
                    canvasWidth / 2,
                    signatureHeight + 20
                );

                previewCtx.font = "10px Arial";
                previewCtx.fillStyle = "#666";
                previewCtx.fillText(
                    "Parent/Guardian:",
                    canvasWidth / 2,
                    signatureHeight + 38
                );
            }

            const dataUrl = elements.previewCanvas.toDataURL("image/png");
            previewCtx.restore();
            return dataUrl;
        }

        // ==================== Modal Canvas (Drawing) ====================

        function initSignaturePad() {
            // Clean up existing instance
            if (signaturePad) {
                signaturePad.off();
                signaturePad = null;
            }

            // Get actual display dimensions
            const displayWidth = elements.modalCanvas.offsetWidth || 600;
            const displayHeight = MODAL_HEIGHT;

            // Set canvas size to match display size (no ratio multiplication)
            elements.modalCanvas.width = displayWidth;
            elements.modalCanvas.height = displayHeight;

            // Set CSS display size
            elements.modalCanvas.style.width = displayWidth + "px";
            elements.modalCanvas.style.height = displayHeight + "px";

            // Initialize SignaturePad without any context scaling
            signaturePad = new SignaturePad(elements.modalCanvas, {
                backgroundColor: "rgb(255, 255, 255)",
                penColor: "rgb(0, 0, 0)",
                minWidth: 0.5,
                maxWidth: 2.5,
                velocityFilterWeight: 0.7,
                dotSize: 1,
            });

            console.log(
                "SignaturePad created, isEmpty:",
                signaturePad.isEmpty()
            );

            window.signaturePad = signaturePad;
        }

        function loadSignatureToModal() {
            if (!signaturePad) return;

            // Clear first
            signaturePad.clear();

            // Load from hidden input if exists
            if (elements.hiddenInput.value) {
                try {
                    signaturePad.fromDataURL(elements.hiddenInput.value);
                } catch (error) {
                    console.error("Error loading signature:", error);
                }
            }
        }

        function drawTextOnModal(name) {
            if (!signaturePad) return;

            // Just clear the pad - don't try to draw text in modal
            signaturePad.clear();
        }

        // ==================== Event Handlers ====================

        // Consent input - Track name changes
        if (elements.consentInput) {
            elements.consentInput.addEventListener("input", function () {
                // Capitalize each word
                this.value = this.value
                    .toLowerCase()
                    .replace(/(^|\s)\S/g, (letter) => letter.toUpperCase());

                // If user modifies the consent name after signing, clear the signature
                // They need to re-sign with the new name
                if (hasActualSignature) {
                    hasActualSignature = false;
                    elements.hiddenInput.value = "";
                    showPlaceholder();
                    console.log(
                        "[signature] Consent name changed - signature cleared, please re-sign"
                    );
                }
            });
        }

        // Open modal button
        if (elements.openBtn && elements.modal) {
            elements.openBtn.addEventListener("click", () => {
                if (!modalInstance) {
                    modalInstance = new bootstrap.Modal(elements.modal, {
                        backdrop: false,
                    });
                }
                modalInstance.show();
            });

            elements.modal.addEventListener("shown.bs.modal", () => {
                console.log("Modal shown, initializing signature pad...");

                // Initialize signature pad after modal is fully shown
                initSignaturePad();

                console.log("SignaturePad initialized:", signaturePad);
                console.log("Canvas element:", elements.modalCanvas);
                console.log("Canvas dimensions:", {
                    width: elements.modalCanvas.width,
                    height: elements.modalCanvas.height,
                    styleWidth: elements.modalCanvas.style.width,
                    styleHeight: elements.modalCanvas.style.height,
                });

                // Store reference globally for debugging
                window.__currentSignaturePad = signaturePad;

                // Small delay to ensure canvas is properly sized
                setTimeout(() => {
                    loadSignatureToModal();
                }, 50);
            });
        }

        // Clear button
        if (elements.clearBtn) {
            elements.clearBtn.addEventListener("click", () => {
                if (!signaturePad) return;
                signaturePad.clear();

                // Clear the actual signature flag and hidden input
                hasActualSignature = false;
                elements.hiddenInput.value = "";
                showPlaceholder();

                console.log("[signature] Signature cleared");
            });
        }

        // Save button
        if (elements.saveBtn) {
            elements.saveBtn.addEventListener("click", () => {
                console.log("[signature] Save clicked");
                console.log("[signature] signaturePad variable:", signaturePad);
                console.log(
                    "[signature] window.__currentSignaturePad:",
                    window.__currentSignaturePad
                );

                // Use the current signaturePad reference
                const pad = signaturePad || window.__currentSignaturePad;

                if (!pad) {
                    console.error("[signature] signaturePad is null!");
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Signature pad not initialized. Please close and reopen the modal.",
                        confirmButtonText: "OK",
                    });
                    return;
                }

                // Ensure user actually drew something on the pad
                if (pad.isEmpty()) {
                    console.log(
                        "[signature] SignaturePad reports empty signature"
                    );
                    Swal.fire({
                        icon: "warning",
                        title: "Empty Signature",
                        text: "Please draw your signature before saving.",
                        confirmButtonText: "OK",
                    });
                    return;
                }

                // Get the signature image data
                let imageData;
                try {
                    imageData = pad.toDataURL("image/png");
                    console.log(
                        "[signature] Image data length:",
                        imageData.length
                    );
                } catch (error) {
                    console.error(
                        "[signature] Error getting image data:",
                        error
                    );
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Failed to read signature. Please try again.",
                        confirmButtonText: "OK",
                    });
                    return;
                }

                // Check if it's truly empty by comparing to a blank canvas
                // Empty signature typically has length around 1400-1800
                // Actual drawing will be significantly longer
                if (imageData.length < 2000) {
                    console.log(
                        "[signature] Signature appears empty (data too short)"
                    );
                    Swal.fire({
                        icon: "warning",
                        title: "Empty Signature",
                        text: "Please draw your signature before saving.",
                        confirmButtonText: "OK",
                    });
                    return;
                }

                console.log("[signature] Valid signature detected, saving...");

                // Mark that we have an actual signature
                hasActualSignature = true;

                // Get the consent name
                const consentName = elements.consentInput
                    ? elements.consentInput.value.trim()
                    : "";

                const compositeData = renderCompositeSignature(
                    elements.modalCanvas,
                    consentName
                );

                if (!compositeData) {
                    console.error(
                        "[signature] Failed to render composite signature"
                    );
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Failed to prepare signature preview. Please try again.",
                        confirmButtonText: "OK",
                    });
                    return;
                }

                elements.hiddenInput.value = compositeData;

                console.log(
                    "[signature] Saved composite signature with name. Length:",
                    elements.hiddenInput.value.length
                );

                // Close modal
                if (modalInstance) {
                    modalInstance.hide();
                }

                // Success notification
                Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                }).fire({
                    icon: "success",
                    title: "Signature saved",
                });
            });
        }

        // ==================== Form Validation ====================

        // CRITICAL: Set onsubmit handler directly (highest priority)
        elements.form.onsubmit = function (e) {
            const signatureValue = elements.hiddenInput.value;
            const isValidSignature =
                hasActualSignature &&
                signatureValue &&
                signatureValue.trim() !== "" &&
                signatureValue.startsWith("data:image");

            console.log("=".repeat(50));
            console.log("[signature] Form onsubmit triggered");
            console.log(
                "[signature] Has actual signature:",
                hasActualSignature
            );
            console.log("[signature] Hidden input value:", signatureValue);
            console.log(
                "[signature] Value length:",
                signatureValue ? signatureValue.length : 0
            );
            console.log(
                "[signature] Starts with 'data:image':",
                signatureValue ? signatureValue.startsWith("data:image") : false
            );
            console.log("[signature] Is valid signature:", isValidSignature);
            console.log("=".repeat(50));

            if (!isValidSignature) {
                console.log(
                    "[signature] BLOCKING SUBMISSION - No valid signature"
                );
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();

                Swal.fire({
                    icon: "warning",
                    title: "Missing Signature",
                    text: "Please open the signature modal and draw your signature before submitting the form.",
                    confirmButtonText: "OK",
                }).then(() => {
                    if (elements.openBtn) {
                        elements.openBtn.click();
                    }
                });
                return false;
            }

            console.log(
                "[signature] Valid signature present - allowing submit"
            );
            return true; // Allow form submission
        };

        // Also add event listener as backup - use capture phase
        elements.form.addEventListener(
            "submit",
            (e) => {
                console.log("[signature] addEventListener submit triggered");

                const signatureValue = elements.hiddenInput.value;
                const isValidSignature =
                    hasActualSignature &&
                    signatureValue &&
                    signatureValue.trim() !== "" &&
                    signatureValue.startsWith("data:image");

                // FIRST CHECK: Signature validation (most critical)
                if (!isValidSignature) {
                    console.log(
                        "[signature] addEventListener BLOCKING - No valid signature"
                    );
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();

                    Swal.fire({
                        icon: "warning",
                        title: "Missing Signature",
                        text: "Please open the signature modal and draw your signature before submitting the form.",
                        confirmButtonText: "OK",
                    }).then(() => {
                        if (elements.openBtn) {
                            elements.openBtn.click();
                        }
                    });
                    return false;
                }

                // Check required fields
                const requiredFields = [
                    { id: "department", name: "Department" },
                    { id: "course", name: "Course/Section" },
                    { id: "year_level", name: "Grade Level" },
                    { id: "last_name", name: "Last Name" },
                    { id: "first_name", name: "First Name" },
                    {
                        id: "consent_by",
                        name: "Consent by (Parent/Guardian Full Name)",
                    },
                ];

                for (const field of requiredFields) {
                    const element = document.getElementById(field.id);
                    if (!element || !element.value.trim()) {
                        e.preventDefault();
                        e.stopImmediatePropagation();

                        Swal.fire({
                            icon: "warning",
                            title: "Required field",
                            text: `Please fill in: ${field.name}`,
                            confirmButtonText: "OK",
                        }).then(() => {
                            if (element) {
                                element.scrollIntoView({
                                    behavior: "smooth",
                                    block: "center",
                                });
                                element.focus();
                            }
                        });
                        return;
                    }
                }

                // Check consent checkboxes
                const consentChecked = Array.from(
                    document.querySelectorAll('input[name="consent[]"]')
                ).some((cb) => cb.checked);

                if (!consentChecked) {
                    e.preventDefault();
                    e.stopImmediatePropagation();

                    Swal.fire({
                        icon: "warning",
                        title: "Consent required",
                        text: "Please select at least one consent option.",
                        confirmButtonText: "OK",
                    });
                    return false;
                }

                // All validations passed
                console.log("All validations passed - form will submit");
            },
            true
        ); // Use capture phase to run BEFORE other handlers

        // ==================== Initialize ====================

        // Load existing signature or show placeholder
        if (elements.hiddenInput.value) {
            hasActualSignature = true; // Existing signature means it was previously saved
            const img = new Image();
            img.onload = () => {
                const ctx = elements.previewCanvas.getContext("2d");
                const rect = elements.previewCanvas.getBoundingClientRect();
                ctx.clearRect(
                    0,
                    0,
                    elements.previewCanvas.width,
                    elements.previewCanvas.height
                );
                ctx.drawImage(img, 0, 0, rect.width, PREVIEW_HEIGHT);
            };
            img.src = elements.hiddenInput.value;
        } else {
            showPlaceholder();
        }

        console.log("[signature] Initialization complete");
    } catch (error) {
        console.error("[signature] Initialization failed:", error);
    }
}

// Expose globally for Blade templates
window.initializeHealthForm = initializeHealthForm;

// Auto-initialize when DOM is ready
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initializeHealthForm);
} else {
    initializeHealthForm();
}
