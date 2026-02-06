<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLINIC - DEMO PORTAL</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo2_pdf.png') }}">
    @php
    function asset_versioned($path) {
    $file = public_path($path);
    if (file_exists($file)) {
    return asset($path) . '?v=' . filemtime($file);
    }
    return asset($path);
    }
    @endphp

    <!-- External CSS Libraries -->
    @vite([
    'resources/css/app.css',
    'resources/css/index.css',
    ])
    <!-- Ensure background image uses correct public path -->
    <style>
        body {
            background-image: url('{{ asset("images/background.jpg") }}');
            background-repeat: no-repeat;
            background-position: center center;
            background-size: cover;
            background-attachment: fixed;
            min-height: 100vh;
            position: relative;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Topbar with Login -->
        <div class="topbar">
            <a href="{{ route('admin.login') }}" class="login-btn">
                <i class="fas fa-right-to-bracket"></i>
                Login
            </a>
        </div>
        <!-- Header -->
        <div class="header">
            <div class="clinic-logo">
                <img src="{{ asset('images/logo2_pdf.png') }}" alt="Samuel Clinic Logo" />
            </div>
            <h1>Clinic System Demo</h1>
            <h2>For Demonstration</h2><br>
            <p>Professional Healthcare Services</p>
            <p>Your Health, Our Priority</p>
        </div>

        @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif

        <!-- Call to Action Section -->
        <div class="cta-section">
            <div class="cta-content">
                <h2 class="cta-title">Student Health Services</h2>
                <p class="cta-subtitle">
                    Log in to manage your health information and stay updated on available services.
                </p>
                <div class="cta-buttons">
                    <a href="#services" onclick="scrollToServices()" class="btn-cta btn-secondary-cta">
                        <i class="fas fa-info-circle"></i>
                        Learn About Our Services
                    </a>
                </div>
            </div>
        </div>

        <!-- Feature badges -->
        <div class="features-row">
            <div class="feature-badge">
                <i class="fas fa-shield-heart"></i>
                <span>Trusted Care</span>
            </div>
            <div class="feature-badge">
                <i class="fas fa-lock"></i>
                <span>Secure Records</span>
            </div>
            <div class="feature-badge">
                <i class="fas fa-bolt"></i>
                <span>Fast Service</span>
            </div>
        </div>

        <!-- Services Section -->
        <div class="patient-section" id="services">
            <h2 class="section-title">Our Services</h2>
            <p class="section-subtitle">Comprehensive healthcare services for Samuelians.</p>

            <div class="services-grid">

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-stethoscope"></i>
                    </div>
                    <h3>General Health Assessment</h3>
                    <p>Provides basic health check-ups and initial assessment for common symptoms and minor health concerns.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <h3>Consultation Management</h3>
                    <p>Records and manages consultation details to support accurate tracking of patient visits and care history.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-notes-medical"></i>
                    </div>
                    <h3>Health Records Management</h3>
                    <p>Maintains organized and secure medical records for demonstration and system testing purposes.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-pills"></i>
                    </div>
                    <h3>Medication Tracking</h3>
                    <p>Tracks prescribed and recommended medications to support proper documentation and follow-up.</p>
                </div>

            </div>

        </div>
    </div>


    @vite([
    'resources/js/app.js',
    'resources/js/index.js',
    ])

    @if(session('success'))
    <script>
        window.showFormOnLoad = true;
    </script>
    @endif
</body>

</html>