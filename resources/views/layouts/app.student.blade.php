<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard')</title>
    <link rel="icon" type="image/png" href="{{ asset_versioned('images/samuel_logo.png') }}">
    @php
    // small helper to append file mtime as cache-busting query string
    function asset_versioned($path) {
    $file = public_path($path);
    if (file_exists($file)) {
    return asset($path) . '?v=' . filemtime($file);
    }
    return asset($path);
    }
    @endphp
    @vite([
    'resources/css/app.css',
    'resources/css/sidebar.css',
    'resources/js/app.js',
    'resources/js/shared/sidebar.js',

    ])

    @stack('styles')
</head>

<body>
    <header class="main-header">
        <button id="toggle-btn" class="btn-general btn-blue btn-sidebar" aria-label="Toggle sidebar">☰</button>
        <h1>Clinic System Demo - Student Portal</h1>
        <div class="user-info">
            @if(session('student_authenticated'))
            <a href="{{ route('student.profile') }}" class="user-profile-link" title="View your profile">
                <i class="bi bi-person-circle me-1" aria-hidden="true"></i>
                <span class="user-name">{{ session('student_email') ?? 'Student' }}</span>
            </a>
            @else
            <div class="user-profile-link">
                <i class="bi bi-person-circle me-1" aria-hidden="true"></i>
                <span class="user-name">Guest</span>
            </div>
            @endif
        </div>
    </header>
    @include('layouts.sidebar.student')
    @yield('content')
    @stack('modals')
    <div id="flash-data" style="display:none;"
        data-success="{{ session('success') ? e(session('success')) : '' }}"
        data-error="{{ session('error') ? e(session('error')) : '' }}"></div>


    <script>
        (function() {
            const el = document.getElementById('flash-data');
            if (!el) return;
            const success = el.getAttribute('data-success');
            const error = el.getAttribute('data-error');
            if (!success && !error) return;

            const start = Date.now();
            const timeout = 2500; // ms

            function tryShow() {
                if (typeof window.Swal !== 'undefined') {
                    try {
                        if (success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: success,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else if (error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: error
                            });
                        }
                    } catch (e) {
                        // swallow to avoid breaking other scripts; useful for debugging in console
                        // eslint-disable-next-line no-console
                        console.warn('Failed to show flash alert', e);
                    }
                } else if (Date.now() - start < timeout) {
                    setTimeout(tryShow, 100);
                } else {
                    // As a fallback, use native alert so user still sees message
                    if (success) alert(success);
                    else if (error) alert(error);
                }
            }

            tryShow();
        })();
    </script>


    @stack('scripts')
</body>

</html>