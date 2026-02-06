@extends('layouts.app')

@section('title', 'Admin Management')

@push('styles')

<style>
    .main-content {
        background: linear-gradient(to bottom right, #f7ead4 0%, #fff9f1 100%) !important;
    }


    .sidebar {
        background: linear-gradient(to bottom right, #b98243 0%, #e2be87 60%, #fff5e6 100%) !important;
    }

    .main-header {
        background: linear-gradient(to bottom right, #f7ead4 0%, #fff9f1 100%) !important;
        border-bottom: 4px solid #b98243;
    }

    .btn-blue {
        background: linear-gradient(to bottom right, #a84300, #ff8c00)!important;
        /* deep burnt orange → strong orange */
        color: #fff;
    }

    .btn-blue:hover {
        background: linear-gradient(to top left, #ff9933, #663300) !important;
        /* lighter orange → dark brownish-orange */
        transform: translateY(-2px);
        /* slight upward lift */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        /* subtle shadow */
    }





    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 24px;
    }

    .card-clean {
        border-radius: 12px;
        box-shadow: 0 8px 28px rgba(15, 23, 42, 0.06);
        border: none;
    }

    .btn-create {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        border-radius: 8px;
        padding: 8px 16px;
        font-weight: 500;
    }

    .btn-create:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        color: white;
    }

    .role-badge {
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 6px;
        font-weight: 500;
    }

    .role-admin {
        background: #fef3c7;
        color: #92400e;
    }

    .role-medical {
        background: #dbeafe;
        color: #1e40af;
    }

    .role-staff {
        background: #d1fae5;
        color: #065f46;
    }

    .table thead,
    .table thead th {
        background: linear-gradient(to bottom, #cc5c1a, #ffa64d) !important;
        color: #fff !important;
        font-weight: 600;
    }

    .table tbody tr {
        border-bottom: 1px solid #e5e7eb;
    }

    .admin-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        margin-right: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 3px solid white;
    }

    .admin-info {
        display: flex;
        align-items: center;
    }

    .admin-name {
        font-weight: 600;
        color: #1f2937;
        font-size: 1rem;
        margin: 0;
    }

    .current-user-badge {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #1e40af;
        font-size: 0.75rem;
        padding: 2px 8px;
        border-radius: 12px;
        font-weight: 500;
        margin-top: 2px;
        display: inline-block;
    }

    .username-badge {
        background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
        color: #374151;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        border: 1px solid #d1d5db;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .modern-role-select {
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 0.875rem;
        font-weight: 500;
        background: white;
        color: #374151;
        transition: all 0.2s ease;
        min-width: 140px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .modern-role-select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .modern-role-select.border-success {
        border-color: #10b981;
        background: #f0fdf4;
    }

    .modern-role-select.border-danger {
        border-color: #ef4444;
        background: #fef2f2;
    }

    .action-group {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        align-items: center;
    }

    /* Role button (dropdown) styling */
    .role-btn {
        border: none;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    }

    .role-btn.role-admin {
        background: #fef3c7;
        color: #92400e;
    }

    .role-btn.role-medical {
        background: #dbeafe;
        color: #1e40af;
    }

    .role-btn.role-staff {
        background: #d1fae5;
        color: #065f46;
    }

    .modern-btn {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        cursor: pointer;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .btn-delete {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #dc2626;
    }

    .btn-delete:hover {
        background: linear-gradient(135deg, #fecaca, #f87171);
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .protected-badge {
        background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
        color: #6b7280;
        font-size: 0.75rem;
        padding: 6px 12px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        font-style: italic;
    }

    .table-header-modern {
        background: linear-gradient(135deg, #1f2937, #374151);
        color: white;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 0.75rem;
    }

    .table-container-modern {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        background: white;
    }

    .empty-state-modern {
        text-align: center;
        padding: 3rem 2rem;
        color: #6b7280;
    }

    .empty-state-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.3;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .admin-avatar {
            width: 40px;
            height: 40px;
            font-size: 0.875rem;
        }

        .modern-role-select {
            min-width: 120px;
            font-size: 0.8rem;
        }

        .action-group {
            flex-direction: column;
            gap: 4px;
        }
    }
</style>
@endpush

@section('content')
<div class="main-content" style="padding: 24px;">
    <div class="container-fluid">
        <div class="page-header">
            <div>
                <h2 class="mb-1">Admin Management</h2>
            </div>
            <button type="button" class="btn-general btn-orange" data-bs-toggle="modal" data-bs-target="#createAdminModal">
                <i class="bi bi-person-plus me-1"></i>
                Create Admin
            </button>
        </div>

        <div class="table-container-modern">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-header-modern">
                        <tr>
                            <th class="px-4 py-3 border-0">Administrator</th>
                            <th class="px-4 py-3 border-0">Username</th>
                            <th class="px-4 py-3 border-0">Role</th>
                            <th class="px-4 py-3 border-0 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        // ensure the current user is displayed first in the list
                        if (is_iterable($admins)) {
                        $sortedAdmins = collect($admins)->sortByDesc(function($a) {
                        return isset($a->is_current_user) ? (bool) $a->is_current_user : false;
                        })->values();
                        } else {
                        $sortedAdmins = collect();
                        }
                        @endphp

                        @forelse($sortedAdmins as $admin)
                        <tr>
                            <!-- Administrator Info -->
                            <td class="px-4 py-4">
                                <div class="admin-info">
                                    <div class="admin-avatar">
                                        {{ $admin->initials }}
                                    </div>
                                    <div>
                                        <h6 class="admin-name">{{ $admin->prefix }} {{ $admin->lastname }}, {{ $admin->firstname }}</h6>
                                        @if($admin->is_current_user)
                                        <span class="current-user-badge">Current User</span>
                                        @endif
                                    </div>
                                </div>
                            </td>


                            <!-- Username -->
                            <td class="px-4 py-4">
                                <span class="username-badge">{{ $admin->username }}</span>
                            </td>

                            <!-- Role -->
                            <td class="px-4 py-4">
                                @if($admin->is_current_user)
                                <div class="d-inline-block">
                                    <button class="role-btn role-{{ $admin->role }}" type="button" disabled aria-disabled="true" title="You cannot change your own role" style="pointer-events: none;">
                                        {{ $admin->role === 'admin' ? 'Administrator' : ($admin->role === 'medical' ? 'Medical' : 'Staff') }}
                                        <i class="bi bi-lock-fill" aria-hidden="true"></i>
                                    </button>
                                </div>
                                @else
                                <div class="dropdown d-inline-block">
                                    <button class="role-btn role-{{ $admin->role }} dropdown-toggle" type="button" id="roleDropdown{{ $admin->id }}" data-bs-toggle="dropdown" aria-expanded="false" data-id="{{ $admin->id }}">
                                        {{ $admin->role === 'admin' ? 'Administrator' : ($admin->role === 'medical' ? 'Medical' : 'Staff') }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="roleDropdown{{ $admin->id }}">
                                        <li><a class="dropdown-item role-option" href="#" data-value="admin">Administrator</a></li>
                                        <li><a class="dropdown-item role-option" href="#" data-value="medical">Medical</a></li>
                                        <li><a class="dropdown-item role-option" href="#" data-value="staff">Staff</a></li>
                                    </ul>
                                </div>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-4">
                                <div class="action-group">
                                    @if($admin->can_be_deleted)
                                    <form action="{{ route('admins.destroy', $admin->id) }}"
                                        method="POST"
                                        class="delete-form d-inline"
                                        data-delete-type="admin"
                                        data-admin-name="{{ $admin->prefix }} {{ $admin->lastname }} {{ $admin->firstname }} ">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="modern-btn btn-delete"
                                            title="Delete Administrator">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @else
                                    <span class="protected-badge">
                                        {{ $admin->is_current_user ? 'Current User' : 'Protected' }}
                                    </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="border-0">
                                <div class="empty-state-modern">
                                    <div class="empty-state-icon">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <p class="mb-0 fw-medium">No administrators found</p>
                                    <small class="text-muted">Create your first administrator to get started</small>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@include('admins.partials.create-modal')
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelects = document.querySelectorAll('.role-select');
        const csrfToken = '{{ csrf_token() }}';

        // Handle role updates via dropdown
        document.querySelectorAll('.role-option').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const newRole = this.dataset.value;
                const dropdown = this.closest('.dropdown');
                const btn = dropdown.querySelector('.role-btn');
                const adminId = btn.dataset.id;

                // optimistic UI change
                btn.textContent = this.textContent;
                btn.classList.remove('role-admin', 'role-medical', 'role-staff');
                btn.classList.add(`role-${newRole}`);

                fetch(`{{ url('admins') }}/${adminId}/role`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            role: newRole
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // show brief feedback then reload to reflect server state
                            btn.classList.add('border-success');
                            setTimeout(() => window.location.reload(), 700);
                        } else {
                            // revert on failure
                            console.error('Failed to update role', data);
                            window.location.reload();
                        }
                    })
                    .catch(err => {
                        console.error('Error updating role', err);
                        window.location.reload();
                    });
            });
        });
    });
</script>
@endpush