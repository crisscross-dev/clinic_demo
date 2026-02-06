</div>

<!-- SweetAlert2 (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Shared alert_message.js (Vite) -->
@vite(['resources/js/shared/alert_message.js'])
@push('sidebar_styles')
<link rel="preload" href="{{ asset('images/logo2_pdf.png') }}" as="image">
@endpush

<div class="sidebar d-flex flex-column" id="sidebar">

    <div class="logo-section">
        <img src="{{ asset('images/logo2_pdf.png') }}" class="sidebar-logo" alt="Clinic Logo">
        <h2>Medical Services Unit (Demo)</h2>
    </div>

    <ul class="nav-links">
        @if(session('student_authenticated'))
        <li data-tooltip="Dashboard">
            <a href="{{ route('student.dashboard') }}"><i class="bi bi-speedometer2 fs-5"></i>
                <span>Dashboard</span></a>
        </li>
        <hr class="sidebar-separator">
        <li data-tooltip="Logout">
            <a href="#" id="sidebar-logout-link">
                <i class="bi bi-box-arrow-right fs-5 me-2"></i>
                <span>Logout</span>
            </a>
        </li>
        @endif
    </ul>

    @if(session('student_authenticated'))
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none logout-form">
        @csrf
    </form>
    @endif
</div>

<script>
    // Sidebar logout link triggers form submit event for SweetAlert2 handling
    document.addEventListener('DOMContentLoaded', function() {
        var logoutLink = document.getElementById('sidebar-logout-link');
        var logoutForm = document.getElementById('logout-form');
        if (logoutLink && logoutForm) {
            logoutLink.addEventListener('click', function(e) {
                e.preventDefault();

                // Clear all health form caches before logout
                if (typeof localStorage !== 'undefined') {
                    try {
                        // Clear all health form cache entries (handles user-specific keys)
                        Object.keys(localStorage).forEach(function(key) {
                            if (key.startsWith('health_form_cache')) {
                                localStorage.removeItem(key);
                            }
                        });
                        // Also clear session-based identifier
                        sessionStorage.removeItem('form_session_id');
                        console.log('All health form caches cleared on logout');
                    } catch (error) {
                        console.error('Error clearing cache on logout:', error);
                    }
                }

                // Dispatch a submit event so alert_message.js can intercept
                var event = new Event('submit', {
                    cancelable: true,
                    bubbles: true
                });
                logoutForm.dispatchEvent(event);
            });
        }
    });
</script>