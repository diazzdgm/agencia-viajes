@extends('layouts.app')

@section('title', 'Descubre España - Las Mejores Activityes Turísticas')
@section('description', 'Encuentra y Booking las mejores Activityes turísticas de España. Tours, excursiones y experiencias únicas te esperan. ¡Booking ahora!')
@section('keywords', 'turismo España, Activityes turísticas, tours España, excursiones, Bookings online, viajes España')

@push('styles')
<style>
    .hero-gradient {
        background: linear-gradient(135deg, 
            rgba(231, 76, 60, 0.9) 0%, 
            rgba(192, 57, 43, 0.8) 50%, 
            rgba(155, 89, 182, 0.9) 100%),
            url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a"><stop offset="0" stop-color="%23ffffff" stop-opacity=".1"/><stop offset="1" stop-color="%23000000" stop-opacity=".1"/></radialGradient></defs><rect width="1000" height="1000" fill="url(%23a)"/></svg>');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        min-height: 60vh;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }

    .hero-gradient::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="%23ffffff20"/><circle cx="20" cy="20" r="1" fill="%23ffffff15"/><circle cx="80" cy="30" r="1.5" fill="%23ffffff10"/><circle cx="30" cy="80" r="1" fill="%23ffffff20"/><circle cx="70" cy="70" r="2" fill="%23ffffff15"/></svg>');
        animation: float 20s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }

    .search-form-card {
        backdrop-filter: blur(20px);
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    }

    .activity-card {
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: none;
        overflow: hidden;
        position: relative;
    }

    .activity-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        transition: left 0.5s;
    }

    .activity-card:hover::before {
        left: 100%;
    }

    .activity-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px;
        transition: transform 0.3s ease;
    }

    .stats-card:hover {
        transform: scale(1.05);
    }

    .feature-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        margin: 0 auto 1rem;
        transition: all 0.3s ease;
    }

    .feature-icon.icon-1 { background: linear-gradient(135deg, #FF6B6B, #FF8E8E); }
    .feature-icon.icon-2 { background: linear-gradient(135deg, #4ECDC4, #44A08D); }
    .feature-icon.icon-3 { background: linear-gradient(135deg, #45B7D1, #96C93D); }
    .feature-icon.icon-4 { background: linear-gradient(135deg, #F093FB, #F5576C); }

    .feature-card:hover .feature-icon {
        transform: scale(1.1) rotate(10deg);
    }

    .pulse-animation {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .section-divider {
        height: 4px;
        background: linear-gradient(90deg, transparent, var(--primary-color), transparent);
        margin: 4rem 0;
        border-radius: 2px;
    }

    @media (max-width: 768px) {
        .hero-gradient {
            min-height: 50vh;
            background-attachment: scroll;
        }
        
        .search-form-card {
            margin: 1rem;
        }
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero-gradient text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="fade-in">
                    <h1 class="display-4 fw-bold mb-4">
                        Descubre la <span class="text-warning">Magia</span> de España
                    </h1>
                    <p class="lead mb-4">
                        Vive experiencias únicas con nuestras Activityes turísticas. 
                        Desde tours culturales hasta aventuras gastronómicas, 
                        tenemos la Activity perfecta para ti.
                    </p>
                    <div class="d-flex flex-wrap gap-3 mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-star text-warning me-2"></i>
                            <span>{{ $stats['popular_activities'] ?? 0 }} Activityes Populares</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-users text-info me-2"></i>
                            <span>{{ $stats['total_people_today'] ?? 0 }} Personas Hoy</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-check text-success me-2"></i>
                            <span>{{ $stats['total_bookings_today'] ?? 0 }} Bookings Hoy</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="search-form-card card border-0 p-4 slide-in-right">
                    <div class="card-body">
                        <h3 class="card-title text-dark mb-4 text-center">
                            <i class="fas fa-search text-primary me-2"></i>
                            Encuentra tu Activity Perfecta
                        </h3>
                        
                        <form action="{{ route('activities.search') }}" method="POST" id="searchForm">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="date" class="form-label text-dark fw-semibold">
                                        <i class="fas fa-calendar text-primary me-1"></i>
                                        Fecha
                                    </label>
                                    <input type="date" 
                                           class="form-control form-control-lg @error('date') is-invalid @enderror" 
                                           id="date" 
                                           name="date"
                                           value="{{ old('date', date('Y-m-d')) }}"
                                           min="{{ date('Y-m-d') }}"
                                           required>
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="people" class="form-label text-dark fw-semibold">
                                        <i class="fas fa-users text-primary me-1"></i>
                                        Personas
                                    </label>
                                    <select class="form-select form-select-lg @error('people') is-invalid @enderror" 
                                            id="people" 
                                            name="people" 
                                            required>
                                        @for($i = 1; $i <= 20; $i++)
                                            <option value="{{ $i }}" {{ old('people', $defaultPeople ?? 2) == $i ? 'selected' : '' }}>
                                                {{ $i }} {{ $i == 1 ? 'Persona' : 'Personas' }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('people')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg pulse-animation">
                                    <span class="btn-text">
                                        <i class="fas fa-search me-2"></i>
                                        Buscar Activityes
                                    </span>
                                    <span class="loading">
                                        <i class="fas fa-spinner fa-spin me-2"></i>
                                        Buscando...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Access Today -->
@if($availableToday && $availableToday->count() > 0)
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-6 fw-bold text-dark">
                <i class="fas fa-bolt text-warning me-2"></i>
                Disponibles Hoy
            </h2>
            <p class="lead text-muted">Booking ahora y disfruta hoy mismo</p>
        </div>
        
        <div class="row g-4">
            @foreach($availableToday as $activity)
                <div class="col-md-4">
                    <div class="card activity-card h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-map-marker-alt fa-2x text-primary"></i>
                            </div>
                            <h5 class="card-title">{{ $activity->title }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($activity->description, 80) }}</p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="h5 text-primary mb-0">{{ $activity->formatted_price }}</span>
                                <div class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star{{ $activity->popularity >= ($i * 20) ? '' : '-o' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="{{ route('activities.show', ['activity' => $activity->id, 'date' => date('Y-m-d'), 'people' => $defaultPeople ?? 2]) }}" 
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i>Ver Detalles
                                </a>
                                <form action="{{ route('bookings.store') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="activity_id" value="{{ $activity->id }}">
                                    <input type="hidden" name="activity_date" value="{{ date('Y-m-d') }}">
                                    <input type="hidden" name="number_of_people" value="{{ $defaultPeople ?? 2 }}">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-shopping-cart me-1"></i>
                                        Comprar - {{ number_format($activity->sample_total_price, 2, ',', '.') }} €
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<div class="section-divider"></div>

<!-- Featured Activities -->
@if($featuredActivities && $featuredActivities->count() > 0)
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-6 fw-bold text-dark">
                <i class="fas fa-star text-warning me-2"></i>
                Activityes Destacadas
            </h2>
            <p class="lead text-muted">Las experiencias más populares de España</p>
        </div>
        
        <div class="row g-4">
            @foreach($featuredActivities as $activity)
                <div class="col-lg-4 col-md-6">
                    <div class="card activity-card h-100">
                        <div class="card-header bg-transparent border-0 text-center">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="badge bg-primary">Popular</div>
                                <div class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star{{ $activity->popularity >= ($i * 20) ? '' : '-o' }}"></i>
                                    @endfor
                                    <small class="text-muted ms-1">({{ $activity->popularity }})</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $activity->title }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($activity->description, 100) }}</p>
                            
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <small class="text-muted d-block">Precio por persona</small>
                                    <strong class="text-primary">{{ $activity->formatted_price }}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Para {{ $defaultPeople ?? 2 }} personas</small>
                                    <strong class="text-success">{{ number_format($activity->sample_total_price, 2, ',', '.') }} €</strong>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    Disponible desde {{ $activity->start_date->format('d/m/Y') }}
                                </small>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <div class="d-grid gap-2">
                                <a href="{{ route('activities.show', ['activity' => $activity->id, 'date' => date('Y-m-d'), 'people' => $defaultPeople ?? 2]) }}" 
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-info-circle me-1"></i>Ver Detalles
                                </a>
                                <form action="{{ route('bookings.store') }}" method="POST" class="quick-book-form">
                                    @csrf
                                    <input type="hidden" name="activity_id" value="{{ $activity->id }}">
                                    <input type="hidden" name="activity_date" value="{{ date('Y-m-d') }}">
                                    <input type="hidden" name="number_of_people" value="{{ $defaultPeople ?? 2 }}">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-shopping-cart me-1"></i>
                                        Bookingr Ahora
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="text-center mt-5">
            <a href="{{ route('activities.index') }}" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-th-large me-2"></i>
                Ver Todas las Activityes
            </a>
        </div>
    </div>
</section>
@endif

<div class="section-divider"></div>

<!-- Features Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-6 fw-bold text-dark">¿Por Qué Elegirnos?</h2>
            <p class="lead text-muted">Tu experiencia perfecta está a un clic de distancia</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6 text-center feature-card">
                <div class="feature-icon icon-1">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h5>Booking Segura</h5>
                <p class="text-muted">Proceso de Booking 100% seguro con confirmación inmediata</p>
            </div>
            <div class="col-lg-3 col-md-6 text-center feature-card">
                <div class="feature-icon icon-2">
                    <i class="fas fa-users"></i>
                </div>
                <h5>Guías Expertos</h5>
                <p class="text-muted">Profesionales locales que conocen cada rincón de España</p>
            </div>
            <div class="col-lg-3 col-md-6 text-center feature-card">
                <div class="feature-icon icon-3">
                    <i class="fas fa-clock"></i>
                </div>
                <h5>Disponibilidad 24/7</h5>
                <p class="text-muted">Booking en cualquier momento, cualquier día del año</p>
            </div>
            <div class="col-lg-3 col-md-6 text-center feature-card">
                <div class="feature-icon icon-4">
                    <i class="fas fa-heart"></i>
                </div>
                <h5>Experiencias Únicas</h5>
                <p class="text-muted">Activityes cuidadosamente seleccionadas para crear recuerdos</p>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
@if(isset($stats))
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card p-4 text-center">
                    <i class="fas fa-map-marked-alt fa-3x mb-3"></i>
                    <h3 class="fw-bold">{{ $stats['total_activities'] ?? 0 }}</h3>
                    <p class="mb-0">Activityes Disponibles</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card p-4 text-center">
                    <i class="fas fa-star fa-3x mb-3"></i>
                    <h3 class="fw-bold">{{ $stats['popular_activities'] ?? 0 }}</h3>
                    <p class="mb-0">Activityes Populares</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card p-4 text-center">
                    <i class="fas fa-calendar-check fa-3x mb-3"></i>
                    <h3 class="fw-bold">{{ $stats['total_bookings_today'] ?? 0 }}</h3>
                    <p class="mb-0">Bookings Hoy</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card p-4 text-center">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <h3 class="fw-bold">{{ $stats['total_people_today'] ?? 0 }}</h3>
                    <p class="mb-0">Viajeros Hoy</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form submission handling
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const btnText = submitBtn.querySelector('.btn-text');
            const loading = submitBtn.querySelector('.loading');
            
            btnText.style.display = 'none';
            loading.style.display = 'inline-block';
            submitBtn.disabled = true;
        });
    }

    // Quick booking forms
    const quickBookForms = document.querySelectorAll('.quick-book-form');
    quickBookForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Procesando...';
            submitBtn.disabled = true;
        });
    });

    // Animate stats on scroll
    const statsSection = document.querySelector('.stats-card');
    if (statsSection) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        });

        document.querySelectorAll('.stats-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });
    }

    // Auto-update date field to today if past date
    const dateInput = document.getElementById('date');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        if (dateInput.value < today) {
            dateInput.value = today;
        }
    }

    // Enhanced form validation
    const form = document.getElementById('searchForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const date = document.getElementById('date').value;
            const people = document.getElementById('people').value;
            
            if (!date || !people) {
                e.preventDefault();
                showAlert('Por favor, completa todos los campos.', 'warning');
                return false;
            }
            
            const selectedDate = new Date(date);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                e.preventDefault();
                showAlert('La fecha debe ser hoy o posterior.', 'warning');
                return false;
            }
        });
    }
});

function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
</script>
@endpush
