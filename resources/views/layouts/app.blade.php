<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Agencia de Viajes - Descubre España')</title>
    <meta name="description" content="@yield('description', 'Descubre las mejores Activityes turísticas de España. Booking tours, excursiones y experiencias únicas con nuestra agencia de viajes.')">
    <meta name="keywords" content="@yield('keywords', 'turismo, España, Activityes, tours, excursiones, viajes, Bookings')">
    <meta name="author" content="Agencia de Viajes España">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'Agencia de Viajes - Descubre España')">
    <meta property="og:description" content="@yield('description', 'Descubre las mejores Activityes turísticas de España')">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('title', 'Agencia de Viajes - Descubre España')">
    <meta property="twitter:description" content="@yield('description', 'Descubre las mejores Activityes turísticas de España')">
    <meta property="twitter:image" content="{{ asset('images/og-image.jpg') }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #e74c3c;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
            --gradient-primary: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            --gradient-secondary: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--dark-color);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .navbar {
            box-shadow: 0 2px 15px rgba(0,0,0,.1);
            backdrop-filter: blur(10px);
        }

        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: var(--gradient-primary);
            border-color: transparent;
            transform: translateY(-2px);
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0,0,0,.08);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,.15);
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(231, 76, 60, 0.25);
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .footer {
            background: var(--dark-color);
            color: white;
            margin-top: auto;
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        .bg-primary {
            background: var(--gradient-primary) !important;
        }

        .hero-section {
            background: linear-gradient(135deg, rgba(231, 76, 60, 0.9), rgba(192, 57, 43, 0.9)), 
                        url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 800"><polygon fill="%23ffffff10" points="0,0 1200,0 1200,600 0,800"/></svg>');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
        }

        .loading {
            display: none;
        }

        .loading.show {
            display: inline-block;
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 60px 0;
            }
            
            .navbar-brand {
                font-size: 1.2rem;
            }
        }

        /* Animation classes */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .slide-in-right {
            animation: slideInRight 0.5s ease-out;
        }

        @keyframes slideInRight {
            from { transform: translateX(100px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>

    @stack('styles')
</head>

<body class="d-flex flex-column min-vh-100">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-compass me-2"></i>
                Agencia España
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active fw-bold' : '' }}" 
                           href="{{ route('home') }}">
                            <i class="fas fa-home me-1"></i>Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('activities.*') ? 'active fw-bold' : '' }}" 
                           href="{{ route('activities.index') }}">
                            <i class="fas fa-map-marked-alt me-1"></i>Activityes
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-list me-1"></i>Categorías
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('home.category', 'popular') }}">
                                <i class="fas fa-star text-warning me-2"></i>Más Populares
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('home.category', 'today') }}">
                                <i class="fas fa-calendar-day text-primary me-2"></i>Disponibles Hoy
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('home.category', 'cheap') }}">
                                <i class="fas fa-euro-sign text-success me-2"></i>Económicas
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('home.category', 'premium') }}">
                                <i class="fas fa-crown text-warning me-2"></i>Premium
                            </a></li>
                        </ul>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('contact') }}">
                            <i class="fas fa-envelope me-1"></i>Contacto
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('about') }}">
                            <i class="fas fa-info-circle me-1"></i>Acerca de
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Breadcrumbs -->
    @if(isset($breadcrumbs) && count($breadcrumbs) > 1)
        <div class="container mt-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    @foreach($breadcrumbs as $breadcrumb)
                        @if($loop->last)
                            <li class="breadcrumb-item active" aria-current="page">
                                {{ $breadcrumb['title'] }}
                            </li>
                        @else
                            <li class="breadcrumb-item">
                                <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a>
                            </li>
                        @endif
                    @endforeach
                </ol>
            </nav>
        </div>
    @endif

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('warning'))
        <div class="container mt-3">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="container mt-3">
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="flex-fill">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5 class="text-white mb-3">
                        <i class="fas fa-compass me-2"></i>
                        Agencia España
                    </h5>
                    <p class="text-light">
                        Descubre las mejores experiencias turísticas de España. 
                        Booking Activityes únicas y vive momentos inolvidables.
                    </p>
                    <div class="social-links">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <h6 class="text-white mb-3">Navegación</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('home') }}" class="text-light text-decoration-none">Inicio</a></li>
                        <li><a href="{{ route('activities.index') }}" class="text-light text-decoration-none">Activityes</a></li>
                        <li><a href="{{ route('about') }}" class="text-light text-decoration-none">Acerca de</a></li>
                        <li><a href="{{ route('contact') }}" class="text-light text-decoration-none">Contacto</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-3">
                    <h6 class="text-white mb-3">Categorías</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('home.category', 'popular') }}" class="text-light text-decoration-none">Más Populares</a></li>
                        <li><a href="{{ route('home.category', 'today') }}" class="text-light text-decoration-none">Disponibles Hoy</a></li>
                        <li><a href="{{ route('home.category', 'cheap') }}" class="text-light text-decoration-none">Económicas</a></li>
                        <li><a href="{{ route('home.category', 'premium') }}" class="text-light text-decoration-none">Premium</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-3">
                    <h6 class="text-white mb-3">Legal</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('terms') }}" class="text-light text-decoration-none">Términos y Condiciones</a></li>
                        <li><a href="{{ route('privacy') }}" class="text-light text-decoration-none">Política de Privacidad</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Política de Cookies</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Ayuda</a></li>
                    </ul>
                </div>
            </div>
            <hr class="border-light">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-light mb-0">
                        &copy; {{ date('Y') }} Agencia España. Todos los derechos Bookingdos.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-light mb-0">
                        <i class="fas fa-heart text-danger"></i> 
                        Hecho con amor en España
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // CSRF Token setup para AJAX
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };
        
        // Setup AJAX headers
        if (typeof axios !== 'undefined') {
            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.head.querySelector('meta[name="csrf-token"]').content;
        }

        // Utility functions
        function showLoading(element) {
            if (element) {
                const loading = element.querySelector('.loading');
                if (loading) loading.classList.add('show');
            }
        }

        function hideLoading(element) {
            if (element) {
                const loading = element.querySelector('.loading');
                if (loading) loading.classList.remove('show');
            }
        }

        function showAlert(message, type = 'success') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const container = document.querySelector('.container');
            if (container) {
                container.insertBefore(alertDiv, container.firstChild);
                
                // Auto-hide after 5 seconds
                setTimeout(() => {
                    alertDiv.remove();
                }, 5000);
            }
        }

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                if (alert.querySelector('.btn-close')) {
                    alert.classList.remove('show');
                    setTimeout(() => alert.remove(), 150);
                }
            });
        }, 5000);
    </script>

    @stack('scripts')
</body>
</html>
