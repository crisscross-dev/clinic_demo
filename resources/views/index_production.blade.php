<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Samuel Clinic - Patient Portal</title>

    <!-- Production-Ready Assets (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        body {
            background-image: url('{{ asset("images/background.png") }}');
            background-repeat: no-repeat;
            background-position: center center;
            background-size: cover;
            background-attachment: fixed;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .overlay {
            background: rgba(0, 0, 0, 0.6);
            min-height: 100vh;
            padding: 20px 0;
        }

        .navbar {
            background-color: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .hero-section {
            background: linear-gradient(135deg, rgba(32, 124, 202, 0.9), rgba(30, 87, 153, 0.9));
            border-radius: 15px;
            padding: 60px 40px;
            margin: 50px 0;
            color: white;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .btn-primary-custom {
            background: linear-gradient(45deg, #1e5799, #207cca);
            border: none;
            padding: 15px 30px;
            font-size: 18px;
            border-radius: 50px;
            transition: all 0.3s ease;
            text-decoration: none;
            color: white;
            display: inline-block;
            margin: 10px;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(32, 124, 202, 0.4);
            color: white;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            margin: 20px 0;
            text-align: center;
            transition: transform 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 3rem;
            color: #207cca;
            margin-bottom: 20px;
        }

        .footer {
            background-color: rgba(30, 87, 153, 0.95);
            color: white;
            padding: 40px 0;
            margin-top: 50px;
        }
    </style>
</head>

<body>
    <div class="overlay">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a class="navbar-brand fw-bold" href="{{ route('index') }}">
                    <i class="fas fa-heartbeat text-primary me-2"></i>
                    Samuel Clinic
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('index') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('about') }}">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('contact') }}">Contact</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="container">
            <div class="hero-section">
                <h1 class="display-4 fw-bold mb-4">
                    <i class="fas fa-hospital-alt me-3"></i>
                    Welcome to Samuel Clinic
                </h1>
                <p class="lead mb-5">
                    Your health and wellness is our priority. Access your patient portal or administration dashboard with ease.
                </p>
                <div class="d-flex flex-wrap justify-content-center">
                    <a href="{{ route('student.login') }}" class="btn-primary-custom">
                        <i class="fas fa-user me-2"></i>
                        Student Portal
                    </a>
                    <a href="{{ route('admin.login') }}" class="btn-primary-custom">
                        <i class="fas fa-user-md me-2"></i>
                        Admin Dashboard
                    </a>
                </div>
            </div>

            <!-- Features -->
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-stethoscope feature-icon"></i>
                        <h4>Health Records</h4>
                        <p>Access your medical history, consultation records, and health information securely.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-calendar-check feature-icon"></i>
                        <h4>Consultation Tracking</h4>
                        <p>Track your appointments and consultation history with our comprehensive system.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-shield-alt feature-icon"></i>
                        <h4>Secure Access</h4>
                        <p>Your health information is protected with industry-standard security measures.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <div class="container text-center">
                <div class="row">
                    <div class="col-md-12">
                        <h5>Clinic System Demo of General Trias, Inc.</h5>
                        <p>Health and Wellness Services Unit</p>
                        <p>Navarro, General Trias City, Cavite</p>
                        <p>&copy; {{ date('Y') }} Samuel Clinic. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>