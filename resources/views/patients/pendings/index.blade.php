@extends('layouts.app')

@push('styles')
<!-- @vite([
'resources/css/admin/pending.css',
]) -->
<style>
    /* Master List — Patients Table Styles */

    /* Layout wrappers */
    html,
    body {
        height: 100%;
    }

    body {
        overflow-y: hidden;
    }

    /* remove page vertical scroll on these pages */
    .main-content {
        padding: 1rem;
        display: flex;
        flex-direction: column;
        height: 100dvh;
        /* occupy full viewport height */
        overflow: hidden;
        /* confine scroll to inner containers */
    }

    .records-scrollable {
        /* Flexible scroll area that fills remaining height under header */
        flex: 1 1 auto;
        min-height: 0;
        /* allow child to shrink below content height */
        /* Let the inner container handle scrolling to keep sticky reliable */
        overflow: hidden;
        /* defer scroll to table container */
    }

    .table-container {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        /* gray-200 */
        border-radius: 8px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        /* Scrolling happens here so thead sticky anchors correctly */
        overflow: auto;
        /* scroll only inside table container */
        max-height: 80vh;
        min-height: 80vh;
        -webkit-overflow-scrolling: touch;
    }

    /* Smaller search input */
    .form-control-head {
        padding: 6px 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        outline: none;
        font-size: 13px;
        transition: all 0.3s ease;
        width: 180px;
        /* reduced width */
    }

    .form-control-head:focus {
        border-color: #007bff;
        box-shadow: 0 0 4px rgba(0, 123, 255, 0.3);
    }

    .dropdown-wrapper {
        position: relative;
        z-index: 1000;
        /* Keep wrapper above nearby elements */
    }

    .dropdown-menu {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        min-width: 220px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        /* gray-200 */
        border-radius: 8px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1),
            0 4px 6px -4px rgba(0, 0, 0, 0.1);
        padding: 6px;
        z-index: 9999;
        /* Ensure dropdown overlays table and sticky headers */
    }

    .dropdown-menu a {
        display: block;
        padding: 6px 8px;
        border-radius: 6px;
        color: #111827;
        text-decoration: none;
        /* remove underline */
    }

    .dropdown-menu a:hover {
        background: #f3f4f6;
        text-decoration: none;
        /* keep underline off on hover */
    }

    .dropdown-menu a.active {
        background: #eff6ff;
        /* blue-50 */
        color: #1d4ed8;
        /* blue-700 */
    }

    /* Upward dropdown */
    .list-header {
        display: flex;
        justify-content: space-between;
    }

    .search-input {
        width: 100%;
        max-width: 280px;
        padding: 8px 10px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
    }

    /* Table */
    .patient-table {
        width: 100%;
        min-width: 900px;
        border-collapse: separate;
        border-spacing: 0;
        table-layout: fixed;
        /* Ensures consistent column widths for thead/tbody alignment */
    }

    .patient-table,
    .patient-table th,
    .patient-table td {
        box-sizing: border-box;
        /* Keep padding/border from shifting widths */
    }

    .patient-table thead th {
        position: -webkit-sticky;
        /* Safari support */
        position: sticky;
        top: 0;
        background: #207cca;
        color: white;
        font-weight: 600;
        text-align: left;
        padding: 8px 10px;
        border-bottom: 1px solid #e5e7eb;
        z-index: 100;
        /* Higher z-index to ensure it stays on top */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        /* Add shadow to make it more visible when sticky */
    }

    .table-pending thead th {
        background: linear-gradient(to bottom, #ffd78c, #ffb347);
        color: #333;
    }

    /* Make thead itself sticky for extra robustness */
    .patient-table thead {
        position: sticky;
        top: 0;
        z-index: 101;
        /* slightly above th to ensure consistent stacking */
        background: #207cca;
    }

    /* Ensure the table itself doesn't interfere with sticky positioning */
    .patient-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        table-layout: fixed;
        /* Ensures consistent column widths for thead/tbody alignment */
        position: relative;
        /* Create stacking context for sticky headers */
    }

    .patient-table tbody td {
        padding: 8px 10px;
        color: #111827;
        /* gray-900 */
        vertical-align: middle;
        font-size: 14px;
        line-height: 2;
        border-bottom: 1px solid #bec2c9;
    }

    .patient-table tbody tr:hover {
        background: #e2e8f0;
    }

    /* Column sizing */
    /* # */
    .patient-table th:nth-child(1),
    .patient-table td:nth-child(1) {
        width: 2%;
        text-align: center;
    }

    .patient-table th:nth-child(2),
    .patient-table td:nth-child(2) {
        width: 7%;
        text-align: center;
    }

    /* Name */
    .patient-table th:nth-child(3),
    .patient-table td:nth-child(3) {
        width: 18%;
    }

    /* Department */
    .patient-table th:nth-child(4),
    .patient-table td:nth-child(4) {
        width: 12%;
    }

    /* Course/Section */
    .patient-table th:nth-child(5),
    .patient-table td:nth-child(5) {
        width: 18%;
    }

    /* Contact */
    .patient-table th:nth-child(6),
    .patient-table td:nth-child(6) {
        width: 12%;
    }

    /* Address */
    .patient-table th:nth-child(7),
    .patient-table td:nth-child(7) {
        width: 20%;
    }

    /* Actions */
    .patient-table th:nth-child(8),
    .patient-table td:nth-child(8) {
        width: 10vh;
        text-align: center;
    }

    /* Keep the Actions column compact */
    .patient-table th:nth-child(8),
    .patient-table td:nth-child(8) {
        white-space: nowrap;
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
        /* gray-500 */
    }

    .py-4 {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }

    .text-center {
        text-align: center;
    }

    /* Action buttons (if partial uses these) */
    .action-buttons {
        display: flex;
        gap: 4px;
        align-items: center;
        justify-content: flex-start;
        flex-wrap: wrap;
    }

    .action-buttons .btn {
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 0.875rem;
        line-height: 1.25rem;
        border: 1px solid transparent;
        transition: background-color 0.15s ease, color 0.15s ease,
            border-color 0.15s ease;
        cursor: pointer;
    }

    /* Actions dropdown (gear) */
    .actions-dropdown {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
    }

    .actions-toggle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        padding: 0;
        border: 1px solid #d1d5db;
        /* gray-300 */
        border-radius: 6px;
        background: #ffffff;
        cursor: pointer;
    }

    .actions-toggle:hover {
        background: #f9fafb;
    }

    .actions-toggle img {
        display: block;
    }

    .actions-menu {
        position: absolute;
        right: 0;
        top: calc(100% + 6px);
        min-width: 180px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        /* gray-200 */
        border-radius: 8px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1),
            0 4px 6px -4px rgba(0, 0, 0, 0.1);
        padding: 6px;
        z-index: 10000;
    }

    .actions-menu .btn,
    .actions-menu a,
    .actions-menu button,
    .actions-menu .menu-item {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        text-align: left;
        padding: 8px 10px;
        border-radius: 6px;
        border: 0;
        background: transparent;
        color: #111827;
        text-decoration: none;
    }

    .actions-menu .btn:hover,
    .actions-menu a:hover,
    .actions-menu button:hover,
    .actions-menu .menu-item:hover {
        background: #f3f4f6;
        text-decoration: none;
    }

    /* Adapt bootstrap-like group into stacked menu */
    .actions-menu .btn-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .actions-menu form {
        margin: 0;
    }

    .actions-menu .btn i,
    .actions-menu .menu-item i {
        margin-right: 8px;
        width: 16px;
        text-align: center;
    }

    /* Optional button color helpers */
    .btn-view {
        background: #eff6ff;
        /* blue-50 */
        color: #1d4ed8;
        /* blue-700 */
        border-color: #bfdbfe;
        /* blue-200 */
    }

    .btn-view:hover {
        background: #dbeafe;
        /* blue-100 */
    }

    .btn-edit {
        background: #ecfdf5;
        /* emerald-50 */
        color: #047857;
        /* emerald-700 */
        border-color: #a7f3d0;
        /* emerald-200 */
    }

    .btn-edit:hover {
        background: #d1fae5;
        /* emerald-100 */
    }

    .btn-delete {
        background: #fef2f2;
        /* red-50 */
        color: #b91c1c;
        /* red-700 */
        border-color: #fecaca;
        /* red-200 */
    }

    .btn-delete:hover {
        background: #fee2e2;
        /* red-100 */
    }

    @media (max-width: 768px) {


        .sidebar-overlay {
            z-index: 10900 !important;
            /* backdrop under the sidebar */
        }

        .sidebar {
            z-index: 11000 !important;
            /* sidebar sits above the overlay */
        }
    }

    .modal-header-green {
        background: linear-gradient(to bottom right,
                #006400,
                #32cd32);
        /* dark green → light green */
        color: #fff;
    }

    .modal-header-blue {
        background: linear-gradient(to bottom right, #1e5799, #207cca);
        color: #fff;
    }

    .modal-header-orange {
        background: linear-gradient(to bottom right, #ffd78c, #ffb74d);
        color: #333;
        /* dark text for readability */
    }

    .icon-custom {
        color: #495057;
    }

    /* Mobile Responsive Styles - Below 600px */
    @media (max-width: 600px) {
        .main-content {
            padding: 0.5rem;
            height: 100vh;
        }

        .list-header-right {
            width: 100%;
            flex-direction: column;
            gap: 0.25rem;
        }

        .list-header-right button,
        .list-header-right .btn {
            width: 100%;
            justify-content: center;
            margin: 0 !important;
        }

        .records-scrollable {
            flex: 1 1 auto;
            min-height: calc(100vh - 250px);
            max-height: calc(100vh - 250px);
            overflow: hidden;
        }

        .table-container {
            /* Scroll happens here to keep sticky headers working */
            max-height: calc(100vh - 285px);
            min-height: 285px;
            height: auto;
            overflow-x: auto;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        .patient-table {
            min-width: 900px;
            width: 100%;
        }

        .patient-table th,
        .patient-table td {
            white-space: nowrap;
        }

        .search-input {
            max-width: 100%;
        }

        .form-control-head {
            width: 100%;
        }

        .list-header form {
            width: 100%;
            margin-bottom: 0 !important;
        }

        .list-header form button {
            width: auto;
        }

        .list-header {
            flex-direction: column;
            gap: 0.25rem;
            margin-bottom: 0.5rem;
        }

        /* Move header-meta below search */
        .list-header>div:first-child {
            order: 2;
        }

        .list-header-right {
            order: 1;
        }

        /* Hide only the status text, not the bulk actions */
        .header-meta>span {
            display: none !important;
        }

        .header-meta .divider {
            display: none !important;
        }

        /* Keep bulk actions visible when shown and style them compactly */
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
            min-width: calc(50% - 0.125rem);
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

        /* Show header-meta container but style it differently */
        .header-meta {
            display: flex !important;
            width: 100%;
            justify-content: center;
            font-size: 14px;
            flex-wrap: wrap;
        }
    }

    /* Extra Small Devices - Below 400px */
    @media (max-width: 400px) {
        .main-content {
            padding: 0.25rem;
            height: 100vh;
        }

        .records-scrollable {
            min-height: calc(100vh - 240px);
            max-height: calc(100vh - 240px);
            overflow: hidden;
        }

        .table-container {
            /* Scroll happens here to keep sticky headers working */
            max-height: calc(100vh - 240px);
            min-height: 240px;
            height: auto;
            overflow-x: auto;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        .patient-table {
            min-width: 900px;
            width: 100%;
        }

        .patient-table th,
        .patient-table td {
            white-space: nowrap;
        }

        .list-header-right button,
        .list-header-right .btn {
            font-size: 13px;
            padding: 8px 12px;
        }
    }

    /* Extra Small Devices tweaks: reduce font sizes, paddings, and truncation */
    @media (max-width: 400px) {

        .patient-table th,
        .patient-table td {
            white-space: nowrap;
            font-size: 12px;
            padding: 6px 8px;
        }

        .patient-table tbody td {
            line-height: 1.3;
        }

        .text-truncate {
            max-width: 200px;
        }

        .header-meta,
        .header-meta strong {
            font-size: 12px;
        }

        .list-header-right button,
        .list-header-right .btn {
            font-size: 12px;
            padding: 6px 10px;
        }

        .actions-toggle {
            width: 28px;
            height: 28px;
        }
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
        /* slightly larger than fs-6 */
        line-height: 1;
    }

    .icon-action-btn:hover {
        background: rgba(13, 110, 253, 0.06);
    }

    .view-patient-btn {
        line-height: 0;
        color: #d47a0bff;
        /* dark text for contrast */
    }

    /* Bulk actions styles */
    .bulk-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 15px;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    .selected-count {
        font-size: 0.9em;
        font-weight: 500;
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

    /* Table checkbox alignment */
    .patient-table th:first-child,
    .patient-table td:first-child {
        text-align: center;
        vertical-align: middle;
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

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
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
                <span>Status: <strong>Pending</strong></span>
                <span class="divider">|</span>
                <span>Total: <strong id="pendingCount">
                        {{ method_exists($pendingPatients, 'total') ? $pendingPatients->total() : $pendingPatients->count() }}
                    </strong></span>
            </div>

            <!-- Bulk Actions in Header -->
            <div class="bulk-actions-header" style="display: none;">
                <span class="divider">|</span>
                <button type="button" id="approveSelected" class="btn-general btn-green btn-sm">
                    <i class="fas fa-check"></i> Approve Selected
                </button>
                <button type="button" id="deleteSelected" class="btn-general btn-red btn-sm">
                    <i class="fas fa-trash"></i> Delete Selected
                </button>
                <span class="selected-count ms-2 text-muted">(<span id="selectedCountNumber">0</span> selected)</span>
            </div>
        </div>
        <div class="list-header-right">
            <form method="GET" action="{{ route('pendings.index') }}" class="d-flex gap-2 mb-3">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search patients..."
                    class="form-control-head" />
                <button type="submit" class="btn-general btn-blue">Search</button>
            </form>
        </div>
    </div>

    {{-- Pending Patients Table --}}
    <div class="records-scrollable">
        <div class="table-container">
            <table class="patient-table table-pending">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAll" class="form-check-input">
                        </th>
                        <th>#</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Course/Section</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingPatients as $patient)
                    <tr data-patient-id="{{ $patient->id }}" style="cursor: pointer;" title="Double-click to view details">
                        <td>
                            <input type="checkbox" class="form-check-input patient-checkbox"
                                value="{{ $patient->id }}" data-name="{{ $patient->full_name }}">
                        </td>
                        <td>{{ $pendingPatients->firstItem() + $loop->index }}</td>

                        <td>
                            <div class="text-truncate">{{ $patient->full_name }}</div>
                        </td>
                        <td>{{ $patient->department ?: '—' }}</td>
                        <td>
                            {{ $patient->course }}
                            @if($patient->year_level)
                            <span class="text-muted">/ {{ $patient->year_level }}</span>
                            @endif
                        </td>
                        <td>{{ $patient->contact_no ?: '—' }}</td>
                        <td>
                            <div class="text-truncate">{{ $patient->address ?: '—' }}</div>
                        </td>
                        <td>
                            <button
                                type="button"
                                class="view-patient-btn btn-general btn-lightgray"
                                data-id="{{ $patient->id }}"
                                aria-label="View patient"
                                title="View patient">
                                <span class="visually-hidden">View</span>
                                <i class="bi bi-eye-fill fs-5" aria-hidden="true"></i>
                            </button>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-user-clock fa-2x mb-2"></i>
                                <p>No pending requests found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>


        {{-- Pagination Controls --}}
        @if($pendingPatients instanceof \Illuminate\Pagination\LengthAwarePaginator && $pendingPatients->hasPages())
        <div class="pagination-footer">
            {{ $pendingPatients->appends(request()->except('page'))->links('vendor.pagination.bootstrap-5') }}
        </div>
        @endif


    </div>
</div>



<!-- Modal -->
<div class="modal fade" id="patientModal" tabindex="-1" aria-labelledby="patientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header modal-header-blue">
                <h5 class="modal-title " id="patientModalLabel">Patient Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="patientModalBody">
                <!-- Details will be loaded here -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
@vite(['resources/js/shared/alert_message.js'])
<script type="module">
    document.addEventListener("DOMContentLoaded", function() {
        // Modal functionality
        const modalElement = document.getElementById("patientModal");
        const modalBody = document.getElementById("patientModalBody");

        // Bulk operations functionality
        const selectAllCheckbox = document.getElementById('selectAll');
        const patientCheckboxes = document.querySelectorAll('.patient-checkbox');
        const bulkActions = document.querySelector('.bulk-actions-header');
        const selectedCountNumber = document.getElementById('selectedCountNumber');
        const approveSelectedBtn = document.getElementById('approveSelected');
        const deleteSelectedBtn = document.getElementById('deleteSelected');

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

        // Approve selected patients
        approveSelectedBtn.addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.patient-checkbox:checked');
            const patientIds = Array.from(checkedBoxes).map(cb => cb.value);
            const patientNames = Array.from(checkedBoxes).map(cb => cb.dataset.name);

            if (patientIds.length === 0) return;

            const patientList = patientNames.slice(0, 5).join('<br>') +
                (patientNames.length > 5 ? `<br><em>...and ${patientNames.length - 5} more</em>` : '');

            Swal.fire({
                title: 'Approve Selected Patients?',
                html: `Are you sure you want to approve these <strong>${patientIds.length} patient(s)</strong>?<br><br>${patientList}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, approve them',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn-general btn-green btn-space',
                    cancelButton: 'btn-general btn-gray btn-space',
                },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    bulkAction('approve', patientIds);
                }
            });
        });

        // Delete selected patients
        deleteSelectedBtn.addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.patient-checkbox:checked');
            const patientIds = Array.from(checkedBoxes).map(cb => cb.value);
            const patientNames = Array.from(checkedBoxes).map(cb => cb.dataset.name);

            if (patientIds.length === 0) return;

            const patientList = patientNames.slice(0, 5).join('<br>') +
                (patientNames.length > 5 ? `<br><em>...and ${patientNames.length - 5} more</em>` : '');

            Swal.fire({
                title: 'Delete Selected Patients?',
                html: `<strong>⚠️ WARNING:</strong> Are you sure you want to <strong>DELETE</strong> these <strong>${patientIds.length} patient(s)</strong>?<br><br>This action cannot be undone!<br><br>${patientList}`,
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
                    bulkAction('delete', patientIds);
                }
            });
        });

        function bulkAction(action, patientIds) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('pendings/bulk') }}/${action}`;
            form.style.display = 'none';

            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            // Add patient IDs
            patientIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'patient_ids[]';
                input.value = id;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }

        // Modal functionality for viewing patient details
        const modal = new window.bootstrap.Modal(modalElement);

        // Double-click to view patient details
        document.querySelectorAll('.patient-table tbody tr[data-patient-id]').forEach(function(row) {
            row.addEventListener('dblclick', function(e) {
                // Don't trigger if clicking on checkbox or action button
                if (e.target.closest('.patient-checkbox') || e.target.closest('.view-patient-btn')) {
                    return;
                }

                const patientId = this.getAttribute('data-patient-id');
                if (patientId) {
                    modalBody.innerHTML = '<p class="text-center">Loading...</p>';

                    fetch(`{{ url('pendings') }}/${patientId}`, {
                            headers: {
                                "X-Requested-With": "XMLHttpRequest"
                            }
                        })
                        .then(res => res.text())
                        .then(html => {
                            modalBody.innerHTML = html;
                            modal.show();
                        })
                        .catch(() => {
                            modalBody.innerHTML = '<p class="text-danger">Failed to load patient details.</p>';
                        });
                }
            });
        });

        document.addEventListener("click", function(e) {
            const button = e.target.closest(".view-patient-btn");
            if (!button) return;
            const patientId = button.getAttribute("data-id");
            if (!patientId) return;

            modalBody.innerHTML = '<p class="text-center">Loading...</p>';

            fetch(`{{ url('pendings') }}/${patientId}`, {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                })
                .then(res => res.text())
                .then(html => {
                    modalBody.innerHTML = html;
                    modal.show();
                })
                .catch(() => {
                    modalBody.innerHTML = '<p class="text-danger">Failed to load patient details.</p>';
                });
        });
    });
</script>
@endpush