<script type="module">
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-view-request')) {
            const btn = e.target.closest('.btn-view-request');
            const patientId = btn.dataset.patientId;
            const patientName = btn.dataset.patientName;
            let reasons = JSON.parse(btn.dataset.reasons);

            // Sort so newest first
            reasons.sort((a, b) => new Date(b.date) - new Date(a.date));

            // Ensure newest is always shown; include all prior entries (show badges for granted/declined)
            const newest = reasons.length ? reasons[0] : null;
            const others = reasons.length > 1 ? reasons.slice(1) : [];

            // Build reasons list HTML (scrollable)
            let reasonsHtml = `
            <div style="max-height: 350px; overflow-y: auto; padding-right: 6px;">
                <div class="list-group mb-3">
        `;

            // Render newest request (always shown)
            if (newest) {
                const formattedDate = new Date(newest.date).toLocaleString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                const newestStatusBadge = newest.status === 'granted' ?
                    '<span class="badge bg-success ms-1">Granted</span>' :
                    (newest.status === 'declined' ? '<span class="badge bg-danger ms-1">Denied</span>' : '');

                reasonsHtml += `
                <div class="list-group-item" style="background-color: #e9f8ec; border-left: 4px solid #28a745; box-shadow: 0 0 6px rgba(40, 167, 69, 0.4);">
                    <div class="d-flex w-100 justify-content-between align-items-start mb-2">
                        <h6 class="mb-0">
                            <i class="bi bi-clock-history text-primary me-2"></i>
                            Request ${reasons.length} <span class="badge bg-primary ms-1">Newest</span> ${newestStatusBadge}
                        </h6>
                        <small class="text-muted">${formattedDate}</small>
                    </div>
                    <p class="mb-0 mt-2" style="white-space: pre-wrap;">${newest.reason}</p>
                </div>
            `;
            }

            // Render previous acted-on requests (granted/declined) — compact style similar to newest
            others.forEach((item, index) => {
                const formattedDate = new Date(item.date).toLocaleString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                const displayNumber = reasons.length - (index + 1);
                const hasStatus = item.status === 'granted' || item.status === 'declined';
                const statusBadge = item.status === 'granted' ?
                    '<span class="badge bg-success ms-1">Granted</span>' :
                    (item.status === 'declined' ? '<span class="badge bg-danger ms-1">Denied</span>' : '');

                reasonsHtml += `
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between align-items-start mb-1">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-clock-history text-primary"></i>
                            <small class="mb-0">Request ${displayNumber} ${hasStatus ? statusBadge : ''}</small>
                        </div>
                        <small class="text-muted">${formattedDate}</small>
                    </div>
                    <p class="mb-0 mt-1 small text-muted" style="white-space: pre-wrap;">${item.reason}</p>
                </div>
            `;
            });

            reasonsHtml += `
                </div>
            </div>
        `;

            Swal.fire({
                title: `<i class="bi bi-person-circle me-2"></i>${patientName}`,
                html: `
                <div class="text-start">
                    <h6 class="text-muted mb-3">Consent Access Request Details</h6>
                    ${reasonsHtml}
                </div>
            `,
                width: '700px',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: '<i class="bi bi-check-circle me-1"></i> Grant Access',
                denyButtonText: '<i class="bi bi-x-circle me-1"></i> Access Denied',
                cancelButtonText: 'Close',
                customClass: {
                    confirmButton: 'btn-general btn-green btn-space',
                    denyButton: 'btn-general btn-red btn-space',
                    cancelButton: 'btn-general btn-gray btn-space',
                },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    grantAccess(patientId, patientName);
                } else if (result.isDenied) {
                    rejectRequest(patientId, patientName);
                }
            });
        }
    });

    // Grant Access
    function grantAccess(patientId, patientName) {
        Swal.fire({
            title: 'Confirm Grant Access',
            text: `Grant consent access to ${patientName}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, grant access',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn-general btn-green btn-space',
                cancelButton: 'btn-general btn-gray btn-space',
            },
            buttonsStyling: false,
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/patients/${patientId}/toggle-consent`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            consent_form: false,
                            status: 'granted'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const row = document.querySelector(`tr[data-patient-id="${patientId}"]`);
                            if (row) row.remove();
                            if (typeof updateTotalCount === 'function') updateTotalCount();
                            if (typeof updateSelectAllState === 'function') updateSelectAllState();
                            if (typeof updateBulkActions === 'function') updateBulkActions();

                            Swal.fire({
                                icon: 'success',
                                title: 'Access Granted',
                                text: `${patientName} can now edit their consent form`,
                                timer: 2000,
                                showConfirmButton: false
                            });

                            if (typeof checkEmptyTable === 'function') checkEmptyTable();
                        } else {
                            throw new Error(data.message || 'Failed to grant access');
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'Failed to grant access'
                        });
                    });
            }
        });
    }

    // Reject Request
    function rejectRequest(patientId, patientName) {
        Swal.fire({
            title: 'Confirm Rejection',
            text: `Reject the consent access request from ${patientName}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, reject request',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn-general btn-red btn-space',
                cancelButton: 'btn-general btn-gray btn-space',
            },
            buttonsStyling: false,
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/patients/${patientId}/toggle-consent`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            consent_form: true,
                            consent_access_requested: false,
                            status: 'declined'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const row = document.querySelector(`tr[data-patient-id="${patientId}"]`);
                            if (row) row.remove();
                            if (typeof updateTotalCount === 'function') updateTotalCount();
                            if (typeof updateSelectAllState === 'function') updateSelectAllState();
                            if (typeof updateBulkActions === 'function') updateBulkActions();

                            Swal.fire({
                                icon: 'success',
                                title: 'Request Rejected',
                                text: `Access request from ${patientName} has been rejected`,
                                timer: 2000,
                                showConfirmButton: false
                            });

                            if (typeof checkEmptyTable === 'function') checkEmptyTable();
                        } else {
                            throw new Error(data.message || 'Failed to reject request');
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'Failed to reject request'
                        });
                    });
            }
        });
    }
</script>