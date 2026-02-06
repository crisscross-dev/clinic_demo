@extends('layouts.app')

@section('title', 'Student Accounts Management')

@push('styles')

<style>
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
        align-items: flex-start;
        margin-bottom: 1rem;
        gap: 1rem;
        flex-wrap: wrap;
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

    /* Column sizing for account list table - 8 columns total */
    /* Checkbox column */
    .patient-table th:nth-child(1),
    .patient-table td:nth-child(1) {
        width: 50px;
        text-align: center;
    }

    /* # column */
    .patient-table th:nth-child(2),
    .patient-table td:nth-child(2) {
        width: 60px;
        text-align: center;
    }

    /* Full Name column */
    .patient-table th:nth-child(3),
    .patient-table td:nth-child(3) {
        width: 20%;
    }

    /* Username (Email) column */
    .patient-table th:nth-child(4),
    .patient-table td:nth-child(4) {
        width: 22%;
    }

    /* Department column */
    .patient-table th:nth-child(5),
    .patient-table td:nth-child(5) {
        width: 18%;
    }

    /* Course/Section column */
    .patient-table th:nth-child(6),
    .patient-table td:nth-child(6) {
        width: 15%;
    }

    /* Year Level column */
    .patient-table th:nth-child(7),
    .patient-table td:nth-child(7) {
        width: 15%;
        text-align: center;
    }

    /* Actions column */
    .patient-table th:nth-child(8),
    .patient-table td:nth-child(8) {
        width: 80px;
        text-align: center;
    }

    /* Utilities */
    .text-truncate {
        display: block;
        max-width: 100%;
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

    /* Action buttons */
    .btn-edit-account {
        padding: 6px 10px;
        font-size: 0.875rem;
        border-radius: 6px;
        border: 1px solid rgba(0, 0, 0, 0.08);
        background: transparent;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-edit-account:hover {
        background: rgba(13, 110, 253, 0.06);
    }

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

    .view-account-btn {
        line-height: 0;
        color: #d47a0bff;
    }

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

    .view-account-btn {
        line-height: 0;
        color: #d47a0bff;
    }

    /* SweetAlert2 Custom Styles */
    .btn-space {
        margin: 0 4px !important;
    }

    /* ============================================
   RESPONSIVE DESIGN - MOBILE & TABLET
   ============================================ */

    /* Tablet (768px and below) */
    @media (max-width: 768px) {
        .main-content {
            padding: 0.75rem;
        }

        .list-header {
            flex-direction: column;
            gap: 0.75rem;
            align-items: stretch;
        }

        .list-header>div:first-child {
            order: 2;
        }

        .list-header-right {
            order: 1;
            width: 100%;
        }

        .list-header-right form {
            width: 100%;
        }

        .form-control-head {
            flex: 1;
            width: auto;
            min-width: 0;
        }

        .header-meta {
            font-size: 0.875rem;
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
        }

        .patient-table {
            min-width: 900px;
        }

        .patient-table th,
        .patient-table td {
            font-size: 0.875rem;
            padding: 10px 8px;
        }

        .btn-general {
            padding: 6px 10px;
            font-size: 0.875rem;
        }

        .bulk-actions-header .btn-sm {
            padding: 4px 10px;
            font-size: 0.8rem;
        }

        .sidebar-overlay {
            z-index: 10900 !important;
        }

        .sidebar {
            z-index: 11000 !important;
        }
    }

    /* Mobile (576px and below) */
    @media (max-width: 576px) {
        .main-content {
            padding: 0.5rem;
            height: 100vh;
        }

        .list-header {
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }

        .header-meta {
            flex-direction: row;
            align-items: center;
            gap: 8px;
            font-size: 0.813rem;
        }

        .header-meta .divider {
            display: none;
        }

        .header-meta span {
            display: inline;
            width: auto;
        }

        .form-control-head {
            font-size: 0.875rem;
            padding: 8px 10px;
        }

        .btn-general {
            padding: 8px 12px;
            font-size: 0.875rem;
            white-space: nowrap;
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
            border-radius: 6px;
        }

        .patient-table th,
        .patient-table td {
            font-size: 0.813rem;
            padding: 8px 6px;
            white-space: nowrap;
        }

        .patient-table th {
            font-size: 0.75rem;
            font-weight: 600;
        }

        .patient-table td .btn-general {
            padding: 6px 8px;
        }

        .patient-table td .btn-general i {
            font-size: 0.875rem;
        }

        .bulk-actions-header {
            flex-wrap: wrap;
            gap: 6px;
        }

        .bulk-actions-header .btn-sm {
            padding: 6px 10px;
            font-size: 0.75rem;
        }

        .bulk-actions-header .selected-count {
            font-size: 0.75rem;
            width: 100%;
            text-align: left;
        }

        .pagination-container {
            padding: 0.5rem;
        }
    }

    /* Extra small devices (400px and below) */
    @media (max-width: 400px) {
        .main-content {
            padding: 0.25rem;
        }

        .records-scrollable {
            min-height: calc(100vh - 240px);
            max-height: calc(100vh - 240px);
            overflow: hidden;
        }

        .header-meta,
        .header-meta strong {
            font-size: 0.75rem;
        }

        .form-control-head {
            font-size: 0.813rem;
            padding: 6px 8px;
        }

        .btn-general {
            padding: 6px 10px;
            font-size: 0.813rem;
        }

        .list-header-right button,
        .list-header-right .btn {
            font-size: 0.75rem;
            padding: 6px 10px;
        }

        .records-scrollable {
            min-height: calc(100vh - 240px);
            max-height: calc(100vh - 240px);
            overflow: hidden;
        }

        .table-container {
            max-height: calc(100vh - 270px);
            min-height: 285px;
            height: auto;
            overflow-x: auto;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        .patient-table {
            min-width: 850px;
        }

        .patient-table th,
        .patient-table td {
            font-size: 0.75rem;
            padding: 6px 4px;
            white-space: nowrap;
        }

        .patient-table tbody td {
            line-height: 1.3;
        }

        .patient-table td .btn-general {
            padding: 4px 6px;
        }

        .text-truncate {
            max-width: 200px;
        }

        .actions-toggle {
            width: 28px;
            height: 28px;
        }
    }
</style>
@endpush

@section('content')
<div class="main-content">
    {{-- Controls: Header + Search --}}
    <div class="list-header">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="header-meta">
                <span>Page: <strong>Student Accounts | <span id="totalCount">{{ method_exists($accounts, 'total') ? $accounts->total() : $accounts->count() }}</span></strong></span>
                <!-- <span class="divider"></span> -->
                <!-- <span>Total Accounts: <strong id="totalCount">
                        {{ method_exists($accounts, 'total') ? $accounts->total() : $accounts->count() }}
                    </strong></span> -->
            </div>
            <div class="dropdown">
                <button class="btn-general btn-blue dropdown-toggle" type="button" id="statusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    {{ ucfirst($status) }} Students
                </button>
                <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                    <li><a class="dropdown-item" href="{{ route('accounts.index', ['status' => 'all']) }}">All</a></li>
                    <li><a class="dropdown-item" href="{{ route('accounts.index', ['status' => 'active']) }}">Active</a></li>
                    <li><a class="dropdown-item" href="{{ route('accounts.index', ['status' => 'inactive']) }}">Inactive</a></li>
                </ul>
            </div>

            <!-- Bulk Actions in Header -->
            <div class="bulk-actions-header" style="display: none;">
                <span class="divider">|</span>
                <button type="button" id="deleteSelected" class="btn-general btn-red btn-sm">
                    <i class="bi bi-trash-fill"></i> Delete (<span id="selectedCountNumber">0</span>) Selected
                </button>
            </div>
        </div>
        <div class="list-header-right">
            <form method="GET" action="{{ route('accounts.index') }}" class="d-flex gap-2">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search accounts..."
                    class="form-control-head" />
                <button type="submit" class="btn-general btn-blue">Search</button>
            </form>
        </div>
    </div>
    {{-- Student Accounts Table --}}
    <div class="records-scrollable">

        <div class="table-container">
            <table class="patient-table table-consent" id="accounts-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAll" class="form-check-input">
                        </th>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Username (Email)</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accounts as $index => $account)
                    <tr data-account-id="{{ $account->id }}"
                        data-email="{{ $account->email }}"
                        data-full-name="{{ $account->full_name }}"
                        data-department="{{ $account->patientInfo->department ?? '-' }}"
                        data-course="{{ $account->patientInfo->course ?? '-' }}"
                        data-year-level="{{ $account->patientInfo->year_level ?? '-' }}"
                        style="cursor: pointer;"
                        title="Double-click to view/edit details">
                        <td>
                            <input type="checkbox" class="form-check-input account-checkbox"
                                value="{{ $account->id }}" data-name="{{ $account->full_name }}">
                        </td>
                        <td>{{ $accounts->firstItem() + $index }}</td>
                        <td>
                            <div class="text-truncate">{{ $account->full_name }}</div>
                        </td>
                        <td>
                            <div class="text-truncate">{{ $account->email }}</div>
                        </td>
                        <td>{{ $account->patientInfo->department ?? 'N/A' }}</td>
                        <td>{{ $account->status }}</td>
                        <td>{{ $account->last_login_human }}</td> {{-- uses carbon in account model --}}


                        <td>
                            <button
                                type="button"
                                class="view-account-btn btn-general btn-lightgray"
                                data-account-id="{{ $account->id }}"
                                data-email="{{ $account->email }}"
                                data-full-name="{{ $account->full_name }}"
                                data-department="{{ $account->patientInfo->department ?? '-' }}"
                                data-course="{{ $account->patientInfo->course ?? '-' }}"
                                data-year-level="{{ $account->patientInfo->year_level ?? '-' }}"
                                aria-label="View / Edit account"
                                title="View / Edit account">
                                <span class="visually-hidden">View</span>
                                <i class="bi bi-eye-fill fs-5" aria-hidden="true"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-people fs-1"></i>
                                <p class="mb-0 mt-2">No student accounts found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-container">
            {{-- Pagination Controls --}}
            @if($accounts instanceof \Illuminate\Pagination\LengthAwarePaginator && $accounts->hasPages())
            <div class="pagination-footer">
                {{ $accounts->appends(request()->except('page'))->links('vendor.pagination.bootstrap-5') }}
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
        const selectAllCheckbox = document.getElementById('selectAll');
        const bulkActions = document.querySelector('.bulk-actions-header');
        const deleteSelectedBtn = document.getElementById('deleteSelected');

        // Select all functionality (only if the checkbox exists)
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const accountCheckboxesLocal = document.querySelectorAll('.account-checkbox');
                accountCheckboxesLocal.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkActions();
            });
        }

        // Individual checkbox change: delegate via container so new rows are covered
        document.addEventListener('change', function(e) {
            if (e.target && e.target.classList && e.target.classList.contains('account-checkbox')) {
                updateSelectAllState();
                updateBulkActions();
            }
        });

        function updateSelectAllState() {
            const accountCheckboxesLocal = document.querySelectorAll('.account-checkbox');
            const checkedCount = document.querySelectorAll('.account-checkbox:checked').length;
            const totalCount = accountCheckboxesLocal.length;

            if (!selectAllCheckbox) return;

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
            const checkedBoxes = document.querySelectorAll('.account-checkbox:checked');
            const count = checkedBoxes.length;
            const selectedCountNumberLocal = document.getElementById('selectedCountNumber');

            if (!bulkActions) return;

            if (count > 0) {
                bulkActions.style.display = 'flex';
                if (selectedCountNumberLocal) selectedCountNumberLocal.textContent = count;
            } else {
                bulkActions.style.display = 'none';
                if (selectedCountNumberLocal) selectedCountNumberLocal.textContent = '0';
            }
        }

        // Delete selected accounts
        if (deleteSelectedBtn) {
            deleteSelectedBtn.addEventListener('click', function() {
                const checkedBoxes = document.querySelectorAll('.account-checkbox:checked');
                const accountIds = Array.from(checkedBoxes).map(cb => cb.value);
                const accountNames = Array.from(checkedBoxes).map(cb => cb.dataset.name);

                if (accountIds.length === 0) return;

                const accountList = accountNames.slice(0, 5).join('<br>') +
                    (accountNames.length > 5 ? `<br><em>...and ${accountNames.length - 5} more</em>` : '');

                Swal.fire({
                    title: 'Delete Selected Accounts?',
                    html: `<strong>⚠️ WARNING:</strong> Are you sure you want to <strong>DELETE</strong> these <strong>${accountIds.length} account(s)</strong>?<br><br>This action cannot be undone!<br><br>${accountList}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete them',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        confirmButton: 'btn-general btn-red btn-space',
                        cancelButton: 'btn-general btn-gray btn-space',
                    },
                    buttonsStyling: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        bulkDeleteAccounts(accountIds);
                    }
                });
            });

            function bulkDeleteAccounts(accountIds) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("accounts.bulk_destroy") }}';
                form.style.display = 'none';

                // Add CSRF token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                // Add account IDs
                accountIds.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'account_ids[]';
                    input.value = id;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            }

            // Double-click to view/edit account details
            document.querySelectorAll('#accounts-table tbody tr[data-account-id]').forEach(function(row) {
                row.addEventListener('dblclick', function(e) {
                    // Don't trigger if clicking on checkbox or clicking directly on the button
                    if (e.target.closest('.account-checkbox') || e.target.closest('td:last-child')) {
                        return;
                    }

                    // Find and click the view button in this row
                    const btn = this.querySelector('.view-account-btn');
                    if (btn) {
                        btn.click();
                    }
                });
            });

            // Edit account
            document.addEventListener('click', function(e) {
                if (e.target.closest('.view-account-btn')) {
                    const btn = e.target.closest('.view-account-btn');
                    const accountId = btn.dataset.accountId;
                    const email = btn.dataset.email;
                    const fullName = btn.dataset.fullName;
                    const department = btn.dataset.department || '-';
                    const course = btn.dataset.course || '-';
                    const yearLevel = btn.dataset.yearLevel || '-';

                    Swal.fire({
                        title: `<i class="bi bi-pencil-square me-2"></i>Edit Account`,
                        html: `
                        <div class="text-start">
                            <p class="mb-3"><strong>Student:</strong> ${fullName}</p>
                            <p class="mb-3"><strong>Department:</strong> ${department}</p>
                            <p class="mb-3"><strong>Course / Year Level:</strong> ${course} - ${yearLevel}</p>
                            <form id="editAccountForm">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email (Username)</label>
                                    <input type="email" class="form-control" id="email" name="email" value="${email}" required>
                                </div>
                            </form>
                        </div>
                    `,
                        width: '600px',
                        showCancelButton: true,
                        showDenyButton: true,
                        denyButtonText: '<i class="bi bi-trash-fill me-1"></i> Delete',
                        confirmButtonText: '<i class="bi bi-check-circle me-1"></i> Update Account',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            confirmButton: 'btn-general btn-blue btn-space',
                            cancelButton: 'btn-general btn-gray btn-space',
                            denyButton: 'btn-general btn-red btn-space'
                        },
                        buttonsStyling: false,
                        preConfirm: () => {
                            const email = document.getElementById('email').value;
                            if (!email) {
                                Swal.showValidationMessage('Email is required');
                                return false;
                            }
                            return {
                                email
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            updateAccount(accountId, result.value);
                        } else if (result.isDenied) {
                            // Confirm delete when user clicks the deny (Delete) button
                            Swal.fire({
                                title: 'Delete Account?',
                                html: `<strong>⚠️ WARNING:</strong> Are you sure you want to delete the account for <strong>${fullName}</strong>?<br><br>This action cannot be undone!`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, delete it',
                                cancelButtonText: 'Cancel',
                                customClass: {
                                    confirmButton: 'btn-general btn-red btn-space',
                                    cancelButton: 'btn-general btn-gray btn-space',
                                },
                                buttonsStyling: false,
                            }).then((res) => {
                                if (res.isConfirmed) {
                                    deleteAccount(accountId, fullName);
                                }
                            });
                        }
                    });
                }
            });
        }

        function updateAccount(accountId, data) {
            fetch(`/accounts/${accountId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Account Updated',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.message || 'Failed to update account');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Failed to update account'
                    });
                });
        }

        // Note: per-row delete buttons were removed in favor of the Delete action inside the edit modal (deny button).

        function deleteAccount(accountId, fullName) {
            fetch(`/accounts/${accountId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove row from table
                        const row = document.querySelector(`tr[data-account-id="${accountId}"]`);
                        if (row) {
                            row.remove();
                        }

                        // Update UI safely
                        adjustTotalCount(-1);
                        updateSelectAllState();
                        updateBulkActions();

                        Swal.fire({
                            icon: 'success',
                            title: 'Account Deleted',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // If table is now empty, render empty state
                        checkEmptyTable();
                    } else {
                        throw new Error(data.message || 'Failed to delete account');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Failed to delete account'
                    });
                });
        }

        // Adjust the displayed total by delta (use negative to decrement)
        function adjustTotalCount(delta) {
            const totalCountEl = document.getElementById('totalCount');
            if (!totalCountEl) return;
            const current = parseInt(totalCountEl.textContent.replace(/[^0-9\-]/g, ''), 10) || 0;
            const updated = Math.max(0, current + delta);
            totalCountEl.textContent = updated;
        }

        function checkEmptyTable() {
            const tableBodyLocal = document.querySelector('#accounts-table tbody');
            if (!tableBodyLocal) return;
            const rows = tableBodyLocal.querySelectorAll('tr[data-account-id]');
            if (rows.length === 0) {
                tableBodyLocal.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-people fs-1"></i>
                                <p class="mb-0 mt-2">No student accounts found</p>
                            </div>
                        </td>
                    </tr>
                `;
            }
        }
    });
</script>
@endpush