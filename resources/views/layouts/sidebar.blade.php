@push('sidebar_styles')
<link rel="preload" href="{{ asset('images/logo2_pdf.png') }}" as="image">
@endpush

<div class="sidebar d-flex flex-column" id="sidebar">

    <div class="logo-section">
        <img src="{{ asset('images/logo2_pdf.png') }}" class="sidebar-logo" alt="Clinic Logo">
        <h2>Medical Services Unit (Demo)</h2>
    </div>

    <ul class="nav-links">
        @if(session('admin_role') == 'admin' || session('admin_role') == 'medical')
        <li data-tooltip="Dashboard">
            <a href="{{ route('admin.dashboard') }}"><i class="bi bi-bar-chart-fill fs-5"></i>
                <span>Dashboard</span></a>
        </li>
        <li data-tooltip="Patient">
            <a href="{{ route('patients.index') }}"><i class="bi bi-people-fill fs-5"></i>
                <span>Patient List</span></a>
        </li>
        <li data-tooltip="Pending">
            <a href="{{ route('pendings.index') }}"><i class="bi bi-envelope-plus-fill fs-5"></i>
                <span>
                    Pending
                    @if($pendingCount > 0)
                    <span class="badge rounded-pill bg-info ms-1">{{ $pendingCount > 99 ? '99+' : $pendingCount }}</span>
                    @endif
                </span></a>
        </li>

        <li class="has-submenu" data-tooltip="Patient">
            <a href="#" class="submenu-toggle"><i class="bi bi-person-vcard fs-5"></i>
                <span>Settings</span>
                <i class="bi bi-chevron-down submenu-arrow"></i>
            </a>
            <ul class="submenu">

                <li data-tooltip="Consent Management">
                    <a href="{{ route('patients.consent.requests') }}"><i class="bi bi-shield-lock-fill fs-5"></i>
                        <span>Consent Requests</span></a>
                </li>
                <li data-tooltip="Student Accounts">
                    <a href="{{ route('accounts.index') }}"><i class="bi bi-person-circle fs-5"></i>
                        <span>Student Accounts</span></a>
                </li>
            </ul>
        </li>
        <li data-tooltip="Inventory">
            <a href="{{ route('inventory.index') }}">
                <i class="bi bi-capsule fs-5 me-2"></i>
                <span>
                    Inventory
                    @if($outOfStockCount > 0)
                    <span class="badge rounded-pill bg-danger ms-1">{{ $outOfStockCount }}</span>
                    @endif
                    @if($lowStockCount > 0)
                    <span class="badge rounded-pill bg-warning text-dark ms-1">{{ $lowStockCount }}</span>
                    @endif
                </span>
            </a>
        </li>

        <hr class="sidebar-separator">
        <li data-tooltip="Logout">
            <a href="#" class="admin-logout-link"
                onclick="event.preventDefault(); clearHealthCacheAndLogout();">
                <i class="bi bi-box-arrow-right fs-5 me-2"></i>
                <span>Logout</span>
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none logout-form">
                @csrf
            </form>
        </li>



        @elseif(session('admin_role') == 'staff')
        <li data-tooltip="Dashboard">
            <a href="{{ route('admin.dashboard') }}"><i class="bi bi-bar-chart-fill fs-5"></i>
                <span>Dashboard</span></a>
        </li>
        <li data-tooltip="Inventory">
            <a href="{{ route('inventory.index') }}">
                <i class="bi bi-capsule fs-5 me-2"></i>
                <span>
                    Inventory
                    @if($outOfStockCount > 0)
                    <span class="badge rounded-pill bg-danger ms-1">{{ $outOfStockCount }}</span>
                    @endif
                    @if($lowStockCount > 0)
                    <span class="badge rounded-pill bg-warning text-dark ms-1">{{ $lowStockCount }}</span>
                    @endif
                </span>
            </a>
        </li>

        <hr class="sidebar-separator">
        <li data-tooltip="Logout">
            <a href="#" class="admin-logout-link"
                onclick="event.preventDefault(); clearHealthCacheAndLogout();">
                <i class="bi bi-box-arrow-right fs-5 me-2"></i>
                <span>Logout</span>
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none logout-form">
                @csrf
            </form>
        </li>

        @endif
    </ul>

</div>

<script>
    // Clear health form cache on logout for admin users
    function clearHealthCacheAndLogout() {
        if (typeof localStorage !== 'undefined') {
            try {
                // Clear all health form cache entries (handles user-specific keys)
                Object.keys(localStorage).forEach(function(key) {
                    if (key.startsWith('health_form_cache')) {
                        localStorage.removeItem(key);
                    }
                });
                // Also clear session-based identifier
                if (typeof sessionStorage !== 'undefined') {
                    sessionStorage.removeItem('form_session_id');
                }
                console.log('All health form caches cleared on logout');
            } catch (error) {
                console.error('Error clearing cache on logout:', error);
            }
        }

        // Submit the logout form
        document.querySelector('.logout-form').requestSubmit();
    }
</script>