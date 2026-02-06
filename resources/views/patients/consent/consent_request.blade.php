@extends('layouts.app')

@section('title', 'Consent Management')

@push('styles')
<style>
    /* Consent Request Table Styles - Based on pending.css */

    /* Layout wrappers */
    html,
    body {
        height: 100%;
    }

    body {
        overflow-y: hidden;
    }

    .main-content {
        padding: 1rem;
        display: flex;
        flex-direction: column;
        height: 100dvh;
        overflow: hidden;
    }

    .records-scrollable {
        flex: 1 1 auto;
        min-height: 0;
        overflow: hidden;
    }

    .table-container {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        overflow: auto;
        max-height: 68vh;
        min-height: 68vh;
        -webkit-overflow-scrolling: touch;
    }

    /* Form control */
    .form-control-head {
        padding: 6px 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        outline: none;
        font-size: 13px;
        transition: all 0.3s ease;
        width: 180px;
    }

    .form-control-head:focus {
        border-color: #007bff;
        box-shadow: 0 0 4px rgba(0, 123, 255, 0.3);
    }

    /* List header */
    .list-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .list-header-right {
        display: flex;
        align-items: center;
    }

    /* Header meta styling */
    .header-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .header-meta .divider {
        color: #dee2e6;
        margin: 0 4px;
    }

    /* Bulk actions in header */
    .bulk-actions-header {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        animation: slideInRight 0.3s ease-out;
    }

    .bulk-actions-header .btn-sm {
        padding: 4px 12px;
        font-size: 0.875rem;
    }

    .bulk-actions-header .selected-count {
        font-size: 0.8em;
        font-weight: 500;
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(-10px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Table styles */
    .patient-table {
        width: 100%;
        min-width: 900px;
        border-collapse: separate;
        border-spacing: 0;
        table-layout: fixed;
        position: relative;
    }

    .patient-table thead th {
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        background: #207cca;
        color: white;
        font-weight: 600;
        text-align: left;
        padding: 8px 10px;
        border-bottom: 1px solid #e5e7eb;
        z-index: 100;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .table-consent thead th {
        background: linear-gradient(to bottom, #1e5799, #207cca);
        color: white;
    }

    .patient-table thead {
        position: sticky;
        top: 0;
        z-index: 101;
    }

    .patient-table tbody td {
        padding: 8px 10px;
        color: #111827;
        vertical-align: middle;
        font-size: 14px;
        line-height: 2;
        border-bottom: 1px solid #bec2c9;
    }

    .patient-table tbody tr:hover {
        background: #e2e8f0;
    }

    /* Column sizing for consent request table - 6 columns total */
    /* Checkbox column */
    .patient-table th:nth-child(1),
    .patient-table td:nth-child(1) {
        width: 30px;
        text-align: center;
    }

    /* # column */
    .patient-table th:nth-child(2),
    .patient-table td:nth-child(2) {
        width: 40px;
        text-align: center;
    }

    /* Student Name column */
    .patient-table th:nth-child(3),
    .patient-table td:nth-child(3) {
        width: 25%;
    }

    /* Department column */
    .patient-table th:nth-child(4),
    .patient-table td:nth-child(4) {
        width: 20%;
    }

    /* Course/Section column */
    .patient-table th:nth-child(5),
    .patient-table td:nth-child(5) {
        width: 25%;
    }

    /* Actions column */
    .patient-table th:nth-child(6),
    .patient-table td:nth-child(6) {
        width: 80px;
        text-align: center;
    }

    /* Utilities */
    .text-truncate {
        display: block;
        max-width: 360px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .text-muted {
        color: #6b7280;
    }

    .py-4 {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }

    .text-center {
        text-align: center;
    }

    /* Checkbox styling */
    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .form-check-input:indeterminate {
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .patient-table th:first-child,
    .patient-table td:first-child {
        text-align: center;
        vertical-align: middle;
    }

    /* Badge styles */
    .lock-badge {
        font-size: 0.85rem;
        padding: 0.35rem 0.75rem;
    }

    /* SweetAlert2 Custom Styles */
    .btn-space {
        margin: 0 4px !important;
    }

    .swal2-html-container .list-group-item {
        border-left: 3px solid #207cca;
        margin-bottom: 10px;
        background-color: #f8f9fa;
    }

    .swal2-html-container .list-group-item:hover {
        background-color: #e9ecef;
    }

    .swal2-html-container .list-group-item h6 {
        color: #207cca;
        font-weight: 600;
    }

    /* Icon-only view button styling for consistency with pending page */
    .icon-action-btn {
        width: 36px;
        height: 36px;
        padding: 0;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border: 1px solid rgba(0, 0, 0, 0.08);
    }

    .icon-action-btn i {
        color: #495057;
        font-size: 1.12rem;
        line-height: 1;
    }

    .icon-action-btn:hover {
        background: rgba(13, 110, 253, 0.06);
    }

    .view-request-btn {
        line-height: 0;
        color: #d47a0bff;
    }

    /* Ensure the Create Schedule button centers its icon and text */
    #createScheduleBtn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        /* keep default padding/width from .btn-general */
    }


    @media (max-width: 768px) {
        .sidebar-overlay {
            z-index: 10900 !important;
        }

        .sidebar {
            z-index: 11000 !important;
        }
    }

    /* Tablet/Medium screens (601px to 800px) - Optimize layout */
    @media (min-width: 601px) and (max-width: 970px) {
        .main-content {
            padding: 0.75rem;
        }

        /* Stack header for better space usage */
        .list-header {
            flex-direction: column;
            gap: 0.5rem;
            align-items: stretch;
        }

        /* Left section with meta */
        .list-header>div:first-child {
            width: 100%;
        }

        /* Right section with buttons */
        .list-header-right {
            width: 100%;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .list-header-right form {
            flex: 1;
            min-width: 250px;
        }

        .form-control-head {
            width: 100%;
        }

        .list-header-right button {
            white-space: nowrap;
        }

        /* Adjust table height for medium screens */
        .records-scrollable {
            flex: 1 1 auto;
            min-height: calc(100vh - 250px);
            max-height: calc(100vh - 250px);
            overflow: hidden;
        }

        .table-container {
            max-height: calc(100vh - 285px);
            min-height: 285px;
            height: auto;
            overflow-x: auto;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Compact table cells */
        .patient-table th,
        .patient-table td {
            padding: 8px 8px;
            font-size: 0.875rem;
        }

        /* Smaller buttons */
        .list-header-right .btn {
            padding: 6px 10px;
            font-size: 0.875rem;
        }

        /* Adjust header meta spacing */
        .header-meta {
            font-size: 0.875rem;
            gap: 6px;
        }
    }

    /* Mobile (600px and below) - Hide header meta and stack buttons */
    @media (max-width: 600px) {
        .main-content {
            padding: 0.5rem;
            height: 100vh;
        }

        /* Hide unnecessary header parts on mobile */
        .header-meta {
            display: none !important;
        }

        /* Keep bulk actions visible but style compactly */
        .bulk-actions-header {
            width: 100%;
            flex-direction: row;
            flex-wrap: wrap;
            gap: 0.25rem;
            margin-top: 0.25rem;
            justify-content: space-between;
        }

        .bulk-actions-header .divider {
            display: none !important;
        }

        .bulk-actions-header button {
            flex: 1;
            min-width: calc(100% - 0.125rem);
            justify-content: center;
            padding: 6px 8px;
            font-size: 13px;
        }

        .bulk-actions-header .selected-count {
            width: 100%;
            text-align: center;
            padding: 4px 8px;
            background: #f8f9fa;
            border-radius: 4px;
            margin: 0 !important;
            font-size: 12px;
        }

        /* Keep header compact, vertical stacking only if needed */
        .list-header {
            flex-direction: column;
            gap: 0.1rem;
            margin-bottom: 0.05rem;
        }

        /* Keep buttons in one line */
        .list-header-right {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            /* allow wrapping if screen is very small */
            justify-content: flex-start;
            gap: 0.25rem;
            /* small spacing between buttons */
        }

        .list-header-right button {
            flex: 1 1 auto;
            min-width: 90px;
            /* prevent buttons from getting too small */
            margin: 0 !important;
            justify-content: center;
            white-space: nowrap;
        }

        /* Keep search bar compact beside buttons */
        .list-header-right form {
            flex: 1 1 100%;
            margin-top: 2px;
        }

        .form-control-head {
            flex: 1;
            width: auto;
        }

        .records-scrollable {
            flex: 1 1 auto;
            min-height: calc(100vh - 250px);
            max-height: calc(100vh - 250px);
            overflow: hidden;
        }

        .table-container {
            max-height: calc(100vh - 285px);
            min-height: 285px;
            height: auto;
            overflow-x: auto;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            margin-top: 0 !important;
        }

        .patient-table {
            min-width: 900px;
            width: 100%;
        }

        .patient-table th,
        .patient-table td {
            font-size: 0.813rem;
            padding: 8px 6px;
            white-space: nowrap;
        }
    }


    /* Extra small devices (400px and below) */
    @media (max-width: 400px) {
        .main-content {
            padding: 0.25rem;
            height: 100vh;
        }

        .list-header {
            gap: 0.1rem;
            margin-bottom: 0.05rem;
        }

        .list-header-right {
            gap: 0.5rem;
        }

        .records-scrollable {
            min-height: calc(100vh - 200px);
            max-height: calc(100vh - 200px);
            overflow: hidden;
        }

        .table-container {
            max-height: calc(100vh - 285px);
            min-height: 285px;
            height: auto;
            overflow-x: auto;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            margin-top: 0 !important;
        }

        .patient-table {
            min-width: 850px;
            width: 100%;
        }

        .patient-table th,
        .patient-table td {
            font-size: 0.75rem;
            padding: 6px 4px;
            white-space: nowrap;
        }
    }
</style>
@endpush

@section('content')
<div class="main-content">
    {{-- Controls: Status + Search --}}
    <div class="list-header">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="header-meta">
                <span>Page: <strong>Consent Access Requests</strong></span>
                <span class="divider">|</span>
                <span>Total Requests:
                    <strong id="patientCount">
                    </strong>
                </span>
            </div>

            <!-- Bulk Actions in Header -->
            <div class="bulk-actions-header" style="display: none;">
                <span class="divider">|</span>
                <button type="button" id="grantSelected" class="btn-general btn-green btn-sm">
                    <i class="bi bi-check-circle-fill"></i> Grant Access
                </button>
                <button type="button" id="denySelected" class="btn-general btn-red btn-sm">
                    <i class="bi bi-x-circle-fill"></i> Access Denied
                </button>
                <span class="selected-count ms-2 text-muted">(<span id="selectedCountNumber">0</span> selected)</span>
            </div>
        </div>
        <div class="list-header-right">
            <button type="button" id="openFormControlModal" class="btn-general btn-blue me-2">
                <i class="bi bi-clock-history"></i> Schedule Access
            </button>
            <button type="button" id="openLockUnlockModal" class="btn-general btn-red me-2">
                <i class="bi bi-lock-fill"></i> Forms Locked
            </button>
            <form method="GET" action="{{ route('patients.consent.requests') }}" class="d-flex gap-2 mb-3">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search students..."
                    class="form-control-head" />
                <button type="submit" class="btn-general btn-blue">Search</button>
            </form>
        </div>
    </div>

    {{-- Consent Requests Table --}}
    <div class="records-scrollable">
        <div class="table-container">
            <table class="patient-table table-consent" id="patients-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAll" class="form-check-input">
                        </th>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>Department</th>
                        <th>Course/Section</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $index => $patient)
                    <tr data-patient-id="{{ $patient->id }}"
                        data-patient-name="{{ $patient->full_name }}"
                        data-reasons="{{ json_encode(optional($patient->consentRequests)->map(function($req) {
                            return [
                                'reason' => $req->consent_reason,
                                'date' => $req->created_at->format('Y-m-d H:i:s'),
                                'status' => $req->status ?? null,
                            ];
                        }) ?? []) }}"
                        style="cursor: pointer;"
                        title="Double-click to view details">
                        <td>
                            <input type="checkbox" class="form-check-input patient-checkbox"
                                value="{{ $patient->id }}" data-name="{{ $patient->full_name }}">
                        </td>
                        <td>{{ $patients->firstItem() + $index }}</td>
                        <td>
                            <div class="text-truncate">{{ $patient->full_name }}</div>
                        </td>
                        <td>{{ $patient->department ?? 'N/A' }}</td>
                        <td>
                            {{ $patient->course ?? 'N/A' }}
                            @if($patient->year_level)
                            <span class="text-muted">/ {{ $patient->year_level }}</span>
                            @endif
                        </td>
                        <td>
                            <button
                                type="button"
                                class="view-request-btn btn-general btn-lightgray btn-view-request"
                                data-patient-id="{{ $patient->id }}"
                                data-patient-name="{{ $patient->full_name }}"
                                data-reasons="{{ json_encode(optional($patient->consentRequests)->map(function($req) {
                                    return [
                                        'reason' => $req->consent_reason,
                                        'date' => $req->created_at->format('Y-m-d H:i:s'),
                                        'status' => $req->status ?? null,
                                    ];
                                }) ?? []) }}"
                                aria-label="View consent request"
                                title="View consent request">
                                <span class="visually-hidden">View</span>
                                <i class="bi bi-eye-fill fs-5" aria-hidden="true"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-inbox fs-1"></i>
                                <p class="mb-0 mt-2">No pending access requests</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-container">
            {{-- Pagination Controls --}}
            @if($patients instanceof \Illuminate\Pagination\LengthAwarePaginator && $patients->hasPages())
            <div class="pagination-footer">
                {{ $patients->appends(request()->except('page'))->links('vendor.pagination.bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
@vite(['resources/js/shared/alert_message.js'])
<script type="module">
    document.addEventListener('DOMContentLoaded', function() {
        // Update lock/unlock button status on page load
        updateLockUnlockButtonStatus();

        const selectAllCheckbox = document.getElementById('selectAll');
        const patientCheckboxes = document.querySelectorAll('.patient-checkbox');
        const bulkActions = document.querySelector('.bulk-actions-header');
        const selectedCountNumber = document.getElementById('selectedCountNumber');
        const grantSelectedBtn = document.getElementById('grantSelected');
        const tableBody = document.querySelector('#patients-table tbody');

        // Select all functionality
        selectAllCheckbox.addEventListener('change', function() {
            patientCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });

        // Individual checkbox change
        patientCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectAllState();
                updateBulkActions();
            });
        });

        function updateSelectAllState() {
            const checkedCount = document.querySelectorAll('.patient-checkbox:checked').length;
            const totalCount = patientCheckboxes.length;

            if (checkedCount === 0) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
            } else if (checkedCount === totalCount) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = true;
            } else {
                selectAllCheckbox.indeterminate = true;
                selectAllCheckbox.checked = false;
            }
        }

        function updateBulkActions() {
            const checkedBoxes = document.querySelectorAll('.patient-checkbox:checked');
            const count = checkedBoxes.length;

            if (count > 0) {
                bulkActions.style.display = 'flex';
                selectedCountNumber.textContent = count;
            } else {
                bulkActions.style.display = 'none';
            }
        }

        // Grant access to selected patients
        grantSelectedBtn.addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.patient-checkbox:checked');
            const patientIds = Array.from(checkedBoxes).map(cb => cb.value);
            const patientNames = Array.from(checkedBoxes).map(cb => cb.dataset.name);

            if (patientIds.length === 0) return;

            const patientList = patientNames.slice(0, 5).join('<br>') +
                (patientNames.length > 5 ? `<br><em>...and ${patientNames.length - 5} more</em>` : '');

            Swal.fire({
                title: 'Grant Consent Access?',
                html: `Are you sure you want to grant consent access to these <strong>${patientIds.length} student(s)</strong>?<br><br>${patientList}`,
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
                    bulkGrantAccess(patientIds);
                }
            });
        });

        function bulkGrantAccess(patientIds) {
            let completed = 0;
            const total = patientIds.length;

            patientIds.forEach((id) => {
                fetch(`/patients/${id}/toggle-consent`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            consent_form: false // Unlock form (0 = unlocked), controller resets consent_access_requested to 0
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove row from table
                            const row = document.querySelector(`tr[data-patient-id="${id}"]`);
                            if (row) {
                                row.remove();
                            }
                        }

                        completed++;
                        if (completed === total) {
                            updateSelectAllState();
                            updateBulkActions();
                            updateTotalCount();

                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: `Access granted to ${total} student(s) successfully`,
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Check if table is empty
                            checkEmptyTable();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        completed++;
                    });
            });
        }

        // Deny access to selected patients
        const denySelectedBtn = document.getElementById('denySelected');
        denySelectedBtn.addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.patient-checkbox:checked');
            const patientIds = Array.from(checkedBoxes).map(cb => cb.value);
            const patientNames = Array.from(checkedBoxes).map(cb => cb.dataset.name);

            if (patientIds.length === 0) return;

            const patientList = patientNames.slice(0, 5).join('<br>') +
                (patientNames.length > 5 ? `<br><em>...and ${patientNames.length - 5} more</em>` : '');

            Swal.fire({
                title: 'Deny Consent Access?',
                html: `Are you sure you want to deny consent access to these <strong>${patientIds.length} student(s)</strong>?<br><br>${patientList}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, deny access',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn-general btn-red btn-space',
                    cancelButton: 'btn-general btn-gray btn-space',
                },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    bulkDenyAccess(patientIds);
                }
            });
        });

        function bulkDenyAccess(patientIds) {
            let completed = 0;
            const total = patientIds.length;

            patientIds.forEach((id) => {
                fetch(`/patients/${id}/toggle-consent`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            consent_form: true, // Keep form locked
                            consent_access_requested: false // Reset request flag
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove row from table
                            const row = document.querySelector(`tr[data-patient-id="${id}"]`);
                            if (row) {
                                row.remove();
                            }
                        }

                        completed++;
                        if (completed === total) {
                            updateSelectAllState();
                            updateBulkActions();
                            updateTotalCount();

                            Swal.fire({
                                icon: 'success',
                                title: 'Access Denied',
                                text: `Access denied for ${total} student(s) successfully`,
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Check if table is empty
                            checkEmptyTable();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        completed++;
                    });
            });
        }

        // Consent modal handlers have been moved to a separate module file
        // `consent_modal.blade.php` which will be included after this script.

        function updateTotalCount() {
            const rows = tableBody.querySelectorAll('tr[data-patient-id]');
            // The template uses `patientCount` for the visible total count element.
            // Fall back to `totalCount` if present to be defensive.
            const totalEl = document.getElementById('totalCount') || document.getElementById('patientCount');
            if (totalEl) totalEl.textContent = rows.length;
        }

        function checkEmptyTable() {
            const rows = tableBody.querySelectorAll('tr[data-patient-id]');
            if (rows.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-inbox fs-1"></i>
                                <p class="mb-0 mt-2">No pending access requests</p>
                            </div>
                        </td>
                    </tr>
                `;
            }
        }

        // Handle viewing multiple reasons
        document.addEventListener('click', function(e) {
            if (e.target.closest('.view-reasons')) {
                const btn = e.target.closest('.view-reasons');
                const patientName = btn.dataset.patientName;
                const reasons = JSON.parse(btn.dataset.reasons);

                // Build reasons list HTML
                let reasonsHtml = '<div class="list-group">';
                Object.entries(reasons).forEach(([date, reason], index) => {
                    const formattedDate = new Date(date).toLocaleString();
                    reasonsHtml += `
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Request ${index + 1}</h6>
                                <small class="text-muted">${formattedDate}</small>
                            </div>
                            <p class="mb-1">${reason}</p>
                        </div>
                    `;
                });
                reasonsHtml += '</div>';

                Swal.fire({
                    title: `Consent Access Requests - ${patientName}`,
                    html: reasonsHtml,
                    icon: 'info',
                    width: '600px',
                    confirmButtonText: 'Close',
                    customClass: {
                        confirmButton: 'btn-general btn-blue',
                    },
                    buttonsStyling: false,
                });
            }
        });

        // Double-click to view consent request details
        document.querySelectorAll('.patient-table tbody tr[data-patient-id]').forEach(function(row) {
            row.addEventListener('dblclick', function(e) {
                // Don't trigger if clicking on checkbox or clicking directly on the button
                if (e.target.closest('.patient-checkbox') || e.target.closest('td:last-child')) {
                    return;
                }

                // Find and click the view button in this row
                const btn = this.querySelector('.btn-view-request');
                if (btn) {
                    btn.click();
                }
            });
        });

        // Lock/Unlock Forms Modal
        document.getElementById('openLockUnlockModal').addEventListener('click', function() {
            showLockUnlockModal();
        });

        // Function to update lock/unlock button status
        function updateLockUnlockButtonStatus() {
            const button = document.getElementById('openLockUnlockModal');

            fetch('/api/consent-forms-status')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const locked = data.locked_count || 0;
                        const unlocked = data.unlocked_count || 0;
                        const total = locked + unlocked;

                        // Determine majority status
                        if (total === 0) {
                            // No forms
                            button.className = 'btn-general btn-gray me-2';
                            button.innerHTML = '<i class="bi bi-shield-lock"></i> No Forms';
                        } else if (locked > unlocked) {
                            // Majority locked
                            button.className = 'btn-general btn-red me-2';
                            button.innerHTML = '<i class="bi bi-lock-fill"></i> Forms Locked';
                        } else if (unlocked > locked) {
                            // Majority unlocked
                            button.className = 'btn-general btn-green me-2';
                            button.innerHTML = '<i class="bi bi-unlock-fill"></i> Forms Unlocked';
                        } else {
                            // Equal split
                            button.className = 'btn-general btn-orange me-2';
                            button.innerHTML = '<i class="bi bi-shield-lock"></i> Mixed Status';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching status:', error);
                });
        }

        // Form Control Modal - Schedule Form Access with Date/Time
        document.getElementById('openFormControlModal').addEventListener('click', function() {
            // Load existing schedules
            loadSchedules();
        });

        function showLockUnlockModal() {
            Swal.fire({
                title: '<i class="bi bi-shield-lock me-2"></i>Lock/Unlock All Consent Forms',
                html: `
                    <div class="text-start">
                        <p class="mb-4 text-muted">Control student access to all consent forms</p>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-3">
                            <button type="button" id="unlockFormsBtn" class="btn-general btn-green" style="padding: 15px;">
                                <i class="bi bi-unlock-fill me-2 fs-5"></i>
                                <div class="d-inline-block text-start">
                                    <strong class="d-block">Unlock All Forms</strong>
                                    <small class="text-muted">Allow all students to edit consent forms</small>
                                </div>
                            </button>
                            <button type="button" id="lockFormsBtn" class="btn-general btn-red" style="padding: 15px;">
                                <i class="bi bi-lock-fill me-2 fs-5"></i>
                                <div class="d-inline-block text-start">
                                    <strong class="d-block">Lock All Forms</strong>
                                    <small class="text-muted">Prevent all students from editing</small>
                                </div>
                            </button>
                        </div>
                    </div>
                `,
                width: '550px',
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'Close',
                customClass: {
                    cancelButton: 'btn-general btn-gray',
                },
                buttonsStyling: false,
                didOpen: () => {
                    // Handle unlock button
                    document.getElementById('unlockFormsBtn').addEventListener('click', function() {
                        Swal.close();
                        confirmBulkFormControl(false, 'unlock', '');
                    });

                    // Handle lock button
                    document.getElementById('lockFormsBtn').addEventListener('click', function() {
                        Swal.close();
                        confirmBulkFormControl(true, 'lock', '');
                    });
                }
            });
        }

        function loadSchedules() {
            Swal.fire({
                title: '<i class="bi bi-clock-history me-2"></i>Consent Form Schedule Manager',
                html: `
                    <div class="row g-4">
                        <!-- Left Column: Active Schedules -->
                        <div class="col-md-6">
                            <div class="border-end pe-3">
                                <h5 class="mb-3"><i class="bi bi-list-ul me-2"></i>Active Schedules</h5>
                                <div id="schedulesList" class="text-start" style="max-height: 450px; overflow-y: auto;">
                                    <div class="text-center text-muted py-4">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2 mb-0">Loading schedules...</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Create Schedule -->
                        <div class="col-md-6">
                            <div class="ps-3">
                                <h5 class="mb-3"><i class="bi bi-plus-circle me-2"></i>Create New Schedule</h5>
                                
                                <!-- Department Selection -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-building me-2"></i>Department
                                    </label>
                                    <select id="scheduleDepartment" class="form-select">
                                        <option value="">All Departments</option>
                                        @foreach($departments as $dept)
                                        <option value="{{ $dept }}">{{ $dept }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Select specific department or apply to all</small>
                                </div>

                                <!-- Start Date/Time -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-calendar-check me-2"></i>Start Date & Time
                                    </label>
                                    <input type="datetime-local" id="scheduleStartTime" class="form-control">
                                    <small class="text-muted">When forms will be unlocked</small>
                                </div>

                                <!-- End Date/Time -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-calendar-x me-2"></i>End Date & Time
                                    </label>
                                    <input type="datetime-local" id="scheduleEndTime" class="form-control">
                                    <small class="text-muted">When forms will be locked</small>
                                </div>

                                <!-- Preview -->
                                <div id="schedulePreview" class="alert alert-info d-none mb-3">
                                    <strong><i class="bi bi-info-circle me-2"></i>Preview:</strong>
                                    <p class="mb-1 small" id="previewText"></p>
                                </div>

                                <hr class="my-3">

                                <!-- Action Buttons -->
                                <div class="d-grid gap-2">
                                    <button type="button" id="createScheduleBtn" class="btn-general btn-blue">
                                        <i class="bi bi-calendar-plus me-2"></i>Create Schedule
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `,
                width: '1100px',
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'Close',
                customClass: {
                    cancelButton: 'btn-general btn-gray',
                    popup: 'schedule-modal-popup'
                },
                buttonsStyling: false,
                didOpen: () => {
                    // Fetch and display schedules
                    fetchSchedules();

                    // Real-time preview
                    const deptSelect = document.getElementById('scheduleDepartment');
                    const startInput = document.getElementById('scheduleStartTime');
                    const endInput = document.getElementById('scheduleEndTime');
                    const preview = document.getElementById('schedulePreview');
                    const previewText = document.getElementById('previewText');

                    function updatePreview() {
                        const dept = deptSelect.value || 'All Departments';
                        const start = startInput.value;
                        const end = endInput.value;

                        if (start && end) {
                            const startDate = new Date(start);
                            const endDate = new Date(end);
                            const duration = Math.round((endDate - startDate) / (1000 * 60 * 60)); // hours

                            previewText.innerHTML = `
                                <strong>${dept}</strong> forms will unlock on 
                                <strong>${startDate.toLocaleString('en-US', { 
                                    month: 'short', 
                                    day: 'numeric', 
                                    year: 'numeric', 
                                    hour: '2-digit', 
                                    minute: '2-digit' 
                                })}</strong> 
                                and lock on 
                                <strong>${endDate.toLocaleString('en-US', { 
                                    month: 'short', 
                                    day: 'numeric', 
                                    year: 'numeric', 
                                    hour: '2-digit', 
                                    minute: '2-digit' 
                                })}</strong> 
                                (${duration} hours duration)
                            `;
                            preview.classList.remove('d-none');
                        } else {
                            preview.classList.add('d-none');
                        }
                    }

                    deptSelect.addEventListener('change', updatePreview);
                    startInput.addEventListener('change', updatePreview);
                    endInput.addEventListener('change', updatePreview);

                    // Create schedule button
                    document.getElementById('createScheduleBtn').addEventListener('click', function() {
                        const department = deptSelect.value;
                        const startTime = startInput.value;
                        const endTime = endInput.value;

                        if (!startTime || !endTime) {
                            Swal.showValidationMessage('Please select both start and end date/time');
                            return;
                        }

                        if (new Date(startTime) >= new Date(endTime)) {
                            Swal.showValidationMessage('End time must be after start time');
                            return;
                        }

                        executeScheduleFormAccess({
                            department,
                            startTime,
                            endTime
                        });
                    });
                }
            });
        }

        function fetchSchedules() {
            fetch('/api/consent-schedules')
                .then(response => response.json())
                .then(data => {
                    displaySchedules(data.schedules || []);
                })
                .catch(error => {
                    console.error('Error fetching schedules:', error);
                    document.getElementById('schedulesList').innerHTML = `
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Failed to load schedules. Please refresh.
                        </div>
                    `;
                });
        }

        function displaySchedules(schedules) {
            const container = document.getElementById('schedulesList');

            if (schedules.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-calendar-x fs-1"></i>
                        <p class="mt-2 mb-0">No active schedules</p>
                        <small>Create a new schedule to get started</small>
                    </div>
                `;
                return;
            }

            let html = '<div class="list-group">';
            schedules.forEach(schedule => {
                const start = new Date(schedule.start_time);
                const end = new Date(schedule.end_time);
                const now = new Date();
                const isActive = now >= start && now <= end;
                const isPending = now < start;
                const isEnded = now > end;

                let statusBadge = '';
                let statusClass = '';

                if (isActive) {
                    statusBadge = '<span class="badge bg-success">Active</span>';
                    statusClass = 'border-success';
                } else if (isPending) {
                    statusBadge = '<span class="badge bg-warning">Pending</span>';
                    statusClass = 'border-warning';
                } else {
                    statusBadge = '<span class="badge bg-secondary">Ended</span>';
                    statusClass = 'border-secondary';
                }

                html += `
                    <div class="list-group-item ${statusClass}" style="border-left: 4px solid;">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="mb-0">
                                <i class="bi bi-building me-2"></i>
                                ${schedule.department || 'All Departments'}
                            </h6>
                            ${statusBadge}
                        </div>
                        <p class="mb-1 small text-muted">
                            <i class="bi bi-calendar-check me-1"></i>
                            <strong>Start:</strong> ${start.toLocaleString('en-US', { 
                                month: 'short', 
                                day: 'numeric', 
                                hour: '2-digit', 
                                minute: '2-digit' 
                            })}
                        </p>
                        <p class="mb-1 small text-muted">
                            <i class="bi bi-calendar-x me-1"></i>
                            <strong>End:</strong> ${end.toLocaleString('en-US', { 
                                month: 'short', 
                                day: 'numeric', 
                                hour: '2-digit', 
                                minute: '2-digit' 
                            })}
                        </p>
                        ${schedule.is_active ? `
                            <button class="btn btn-sm btn-outline-danger mt-2 deactivate-schedule" data-id="${schedule.id}">
                                <i class="bi bi-x-circle me-1"></i>Deactivate
                            </button>
                        ` : ''}
                    </div>
                `;
            });
            html += '</div>';

            container.innerHTML = html;

            // Add deactivate handlers
            document.querySelectorAll('.deactivate-schedule').forEach(btn => {
                btn.addEventListener('click', function() {
                    const scheduleId = this.dataset.id;
                    deactivateSchedule(scheduleId);
                });
            });
        }

        function deactivateSchedule(scheduleId) {
            Swal.fire({
                title: 'Deactivate Schedule?',
                text: 'This schedule will be stopped immediately.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, deactivate',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn-general btn-red btn-space',
                    cancelButton: 'btn-general btn-gray btn-space',
                },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/api/consent-schedules/${scheduleId}/deactivate`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                fetchSchedules(); // Refresh list
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deactivated',
                                    text: 'Schedule has been deactivated',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }
                        });
                }
            });
        }

        function scheduleFormAccess(scheduleData) {
            Swal.fire({
                title: 'Confirm Schedule',
                html: `
                    <div class="text-start">
                        <p><strong>Department:</strong> ${scheduleData.department || 'All Departments'}</p>
                        <p><strong>Start:</strong> ${new Date(scheduleData.startTime).toLocaleString()}</p>
                        <p><strong>End:</strong> ${new Date(scheduleData.endTime).toLocaleString()}</p>
                        <hr>
                        <p class="text-muted mb-0">Forms will automatically unlock at start time and lock at end time.</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Confirm Schedule',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn-general btn-blue btn-space',
                    cancelButton: 'btn-general btn-gray btn-space',
                },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    executeScheduleFormAccess(scheduleData);
                }
            });
        }

        function executeScheduleFormAccess(scheduleData) {
            Swal.fire({
                title: 'Scheduling...',
                html: 'Setting up scheduled form access...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('/patients/schedule-consent-access', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(scheduleData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Schedule Created',
                            text: data.message || 'Form access has been scheduled successfully',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    } else {
                        throw new Error(data.message || 'Failed to schedule form access');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Failed to schedule form access'
                    });
                });
        }

        function confirmBulkFormControl(lockStatus, action, department = '') {
            const actionText = action === 'lock' ? 'Lock' : 'Unlock';
            const actionIcon = action === 'lock' ? 'lock-fill' : 'unlock-fill';
            const actionColor = action === 'lock' ? 'red' : 'green';
            const deptText = department ? ` for <strong>${department}</strong>` : '';

            Swal.fire({
                title: `Confirm ${actionText} Forms?`,
                html: `
                    <p>Are you sure you want to <strong>${action}</strong> consent forms${deptText}?</p>
                    <p class="text-muted mb-0">
                        ${action === 'lock' 
                            ? 'Students will not be able to edit their consent forms.' 
                            : 'Students will be able to edit their consent forms.'}
                    </p>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: `<i class="bi bi-${actionIcon} me-1"></i> Yes, ${actionText}`,
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: `btn-general btn-${actionColor} btn-space`,
                    cancelButton: 'btn-general btn-gray btn-space',
                },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    executeBulkFormControl(lockStatus, action, department);
                }
            });
        }

        function executeBulkFormControl(lockStatus, action, department = '') {
            // Show loading
            const deptText = department ? ` for ${department}` : '';
            Swal.fire({
                title: 'Processing...',
                html: `${action === 'lock' ? 'Locking' : 'Unlocking'} consent forms${deptText}...`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('/patients/bulk-toggle-consent', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        consent_form: lockStatus, // true = lock (1), false = unlock (0)
                        department: department
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message || `All consent forms have been ${action}ed successfully`,
                            timer: 2500,
                            showConfirmButton: false
                        });

                        // Update button status after action
                        updateLockUnlockButtonStatus();
                    } else {
                        throw new Error(data.message || `Failed to ${action} forms`);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || `Failed to ${action} consent forms`
                    });
                });
        }
    });
</script>
@endpush

@include('patients.consent.consent_modal')