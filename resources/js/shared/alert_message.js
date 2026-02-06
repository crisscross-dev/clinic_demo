// Add z-index fix for SweetAlert2 once
(function () {
    if (!document.getElementById("swal2-zindex-style")) {
        const style = document.createElement("style");
        style.id = "swal2-zindex-style";
        style.textContent = `.swal2-container { z-index: 20000 !important; }`;
        document.head.appendChild(style);
    }
})();

// Attach confirmation dialogs
(function () {
    if (window.__actionConfirmBound) return;
    window.__actionConfirmBound = true;

    document.addEventListener("submit", function (e) {
        const form = e.target;
        if (!(form instanceof HTMLFormElement)) return;

        // DELETE
        if (form.classList.contains("delete-form")) {
            e.preventDefault();

            const deleteType = form.dataset.deleteType || "record";
            let deleteMessage = "This record will be permanently deleted.";

            if (deleteType === "patient") {
                deleteMessage =
                    "This patient record will be permanently deleted.";
            } else if (deleteType === "consultation") {
                deleteMessage =
                    "This consultation record will be permanently deleted.";
            } else if (deleteType === "InventoryItem") {
                deleteMessage = "This Item will be permanently deleted.";
            } else if (deleteType === "category") {
                deleteMessage = "This category will be permanently deleted.";
            } else if (deleteType === "file") {
                deleteMessage = "This file will be permanently deleted.";
            } else if (deleteType === "admin") {
                const adminName = form.dataset.adminName || "";
                deleteMessage = adminName
                    ? `${adminName} will be permanently deleted.`
                    : "This admin account will be permanently deleted.";
            } else {
                Swal.fire({
                    title: "Unauthorized Action",
                    text: "You are not authorized to perform this delete action.",
                    icon: "error",
                    confirmButtonText: "OK",
                    customClass: {
                        confirmButton: "btn-general btn-blue btn-space",
                    },
                    buttonsStyling: false,
                });
                return;
            }

            Swal.fire({
                title: "Are you sure?",
                text: deleteMessage,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it",
                cancelButtonText: "Cancel",
                customClass: {
                    confirmButton: "btn-general btn-red btn-space",
                    cancelButton: "btn-general btn-gray btn-space",
                },
                buttonsStyling: false,
            }).then((result) => {
                if (!result.isConfirmed) return;

                // If this is a file delete, perform AJAX delete so the server JSON
                // doesn't replace the current page. Otherwise fall back to form.submit().
                if ((form.dataset.deleteType || "").toLowerCase() === "file") {
                    // read CSRF token from the form
                    const tokenInput = form.querySelector(
                        'input[name="_token"]'
                    );
                    const csrfToken = tokenInput ? tokenInput.value : null;

                    fetch(form.action, {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": csrfToken,
                            "X-Requested-With": "XMLHttpRequest",
                            Accept: "application/json",
                        },
                    })
                        .then((res) =>
                            res.json().catch(() => ({ success: res.ok }))
                        )
                        .then((data) => {
                            if (data && data.success) {
                                // remove any DOM element that represents this file
                                const fileId = form.dataset.fileId;
                                if (fileId) {
                                    const selector = `[data-file-id="${fileId}"]`;
                                    const el = document.querySelector(selector);
                                    if (el && el.parentNode)
                                        el.parentNode.removeChild(el);
                                }

                                // if the uploads script exposes renderFiles(), call it to refresh UI
                                if (typeof window.renderFiles === "function") {
                                    try {
                                        window.renderFiles();
                                    } catch (e) {
                                        /* ignore */
                                    }
                                }

                                Swal.fire(
                                    "Deleted",
                                    "File has been deleted",
                                    "success"
                                );
                            } else {
                                const msg =
                                    data && data.message
                                        ? data.message
                                        : "Delete failed";
                                Swal.fire("Error", msg, "error");
                            }
                        })
                        .catch((err) => {
                            console.error("Delete error:", err);
                            Swal.fire(
                                "Error",
                                err.message || "Delete failed",
                                "error"
                            );
                        });
                } else {
                    form.submit();
                }
            });
        }

        // APPROVE
        else if (form.classList.contains("approve-form")) {
            e.preventDefault();

            const approveType = form.dataset.approveType || "record";
            let approveMessage = "This record will be approved.";

            if (approveType === "patient") {
                approveMessage = "This patient record will be approved.";
            } else if (approveType === "consultation") {
                approveMessage = "This consultation record will be approved.";
            } else {
                Swal.fire({
                    title: "Unauthorized Action",
                    text: "You are not authorized to perform this approve action.",
                    icon: "error",
                    confirmButtonText: "OK",
                    customClass: {
                        confirmButton: "btn-general btn-blue btn-space",
                    },
                    buttonsStyling: false,
                });
                return;
            }

            Swal.fire({
                title: "Approve this record?",
                text: approveMessage,
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Yes, approve it",
                cancelButtonText: "Cancel",
                customClass: {
                    confirmButton: "btn-general btn-green btn-space",
                    cancelButton: "btn-general btn-gray btn-space",
                },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        // LOGOUT
        else if (form.classList.contains("logout-form")) {
            e.preventDefault();

            Swal.fire({
                title: "Confirm Logout",
                text: "You will be logged out of your account.",
                icon: "warning", // ⚠️ warning is clearer for logout
                showCancelButton: true,
                confirmButtonText: "Yes, log me out",
                cancelButtonText: "Stay logged in",
                reverseButtons: true, // 🔄 put cancel button first (safer UX)
                customClass: {
                    confirmButton: "btn-general btn-red btn-space",
                    cancelButton: "btn-general btn-gray btn-space",
                },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Logging out...",
                        text: "Please wait.",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading(); // ⏳ show spinner
                        },
                    });
                    form.submit();
                }
            });
        }

    });
})();
