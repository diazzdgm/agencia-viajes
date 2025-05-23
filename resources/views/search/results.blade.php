@extends('layouts.app')

@section('title', 'Resultados de Búsqueda - Activityes para ' . $numberOfPeople . ' personas el ' . \Carbon\Carbon::parse($searchDate)->format('d/m/Y'))
@section('description', 'Encuentra las mejores Activityes disponibles para ' . $numberOfPeople . ' personas el ' . \Carbon\Carbon::parse($searchDate)->format('d/m/Y') . '. Booking ahora y vive experiencias únicas.')

@push('styles')
<style>
    .search-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        padding: 3rem 0;
        position: relative;
        overflow: hidden;
    }

    .search-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="%23ffffff15"/><circle cx="80" cy="80" r="3" fill="%23ffffff10"/><circle cx="40" cy="60" r="1" fill="%23ffffff20"/></svg>');
        opacity: 0.3;
    }

    .activity-result-card {
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: none;
        border-left: 4px solid transparent;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        overflow: hidden;
        position: relative;
    }

    .activity-result-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        border-left-color: var(--primary-color);
    }

    .activity-result-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(231, 76, 60, 0.1), transparent);
        transition: left 0.6s ease;
    }

    .activity-result-card:hover::before {
        left: 100%;
    }

    .popularity-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        z-index: 10;
    }

    .popularity-stars {
        color: #ffc107;
        font-size: 0.9rem;
    }

    .price-display {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border-radius: 15px;
        padding: 1rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .price-display::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
        animation: price-shine 3s ease-in-out infinite;
    }

    @keyframes price-shine {
        0%, 100% { transform: scale(0); opacity: 0; }
        50% { transform: scale(1); opacity: 1; }
    }

    .btn-comprar {
        background: linear-gradient(135deg, var(--primary-color) 0%, #c0392b 100%);
        border: none;
        border-radius: 25px;
        padding: 12px 30px;
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-comprar::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s ease;
    }

    .btn-comprar:hover::before {
        left: 100%;
    }

    .btn-comprar:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(231, 76, 60, 0.4);
        color: white;
    }

    .btn-comprar:active {
        transform: translateY(0);
    }

    .search-summary-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .search-summary-card::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 50%);
        animation: float 6s ease-in-out infinite;
    }

    .no-results {
        text-align: center;
        padding: 4rem 2rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 20px;
        margin: 2rem 0;
    }

    .activity-meta {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .activity-meta i {
        width: 16px;
        text-align: center;
    }

    .result-counter {
        background: rgba(255, 255, 255, 0.2);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        backdrop-filter: blur(10px);
    }

    .modify-search-btn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }

    @media (max-width: 768px) {
        .search-header {
            padding: 2rem 0;
        }
        
        .activity-result-card {
            margin-bottom: 1.5rem;
        }
        
        .price-display {
            margin-top: 1rem;
        }
        
        .modify-search-btn {
            bottom: 10px;
            right: 10px;
            width: 50px;
            height: 50px;
        }
    }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .loading-overlay.show {
        display: flex;
    }
</style>
@endpush

@section('content')
<!-- Search Header -->
<section class="search-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-3">
                    <i class="fas fa-search me-3"></i>
                    Resultados de Búsqueda
                </h1>
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar text-warning me-2"></i>
                        <span class="fw-semibold">{{ \Carbon\Carbon::parse($searchDate)->format('d/m/Y') }}</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-users text-info me-2"></i>
                        <span class="fw-semibold">{{ $numberOfPeople }} {{ $numberOfPeople == 1 ? 'Persona' : 'Personas' }}</span>
                    </div>
                    @if($activities->count() > 0)
                        <div class="result-counter">
                            <i class="fas fa-list me-2"></i>
                            {{ $activities->count() }} Activity{{ $activities->count() != 1 ? 'es' : '' }} Encontrada{{ $activities->count() != 1 ? 's' : '' }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="{{ route('home') }}" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-search me-2"></i>
                    Nueva Búsqueda
                </a>
            </div>
        </div>
    </div>
</section>

<div class="container my-5">
    @if($activities->count() > 0)
        <!-- Search Summary -->
        <div class="search-summary-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="mb-2">
                        <i class="fas fa-map-marked-alt me-2"></i>
                        ¡Perfecto! Encontramos {{ $activities->count() }} Activityes para ti
                    </h3>
                    <p class="mb-0 opacity-75">
                        Activityes ordenadas por popularidad • Precios calculados para {{ $numberOfPeople }} {{ $numberOfPeople == 1 ? 'persona' : 'personas' }}
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <div class="d-flex justify-content-center justify-content-md-end align-items-center gap-3">
                        <div class="text-center">
                            <div class="h4 mb-0">{{ $activities->where('popularity', '>', 80)->count() }}</div>
                            <small>Muy Populares</small>
                        </div>
                        <div class="text-center">
                            <div class="h4 mb-0">{{ $activities->where('price_per_person', '<', 50)->count() }}</div>
                            <small>Económicas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activities Results -->
        <div class="row g-4">
            @foreach($activities as $activity)
                <div class="col-12">
                    <div class="card activity-result-card h-100">
                        <!-- Popularity Badge -->
                        @if($activity->popularity > 80)
                            <div class="popularity-badge">
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-fire me-1"></i>
                                    Muy Popular
                                </span>
                            </div>
                        @elseif($activity->popularity > 60)
                            <div class="popularity-badge">
                                <span class="badge bg-primary">
                                    <i class="fas fa-star me-1"></i>
                                    Popular
                                </span>
                            </div>
                        @endif

                        <div class="card-body">
                            <div class="row align-items-center">
                                <!-- Activity Info -->
                                <div class="col-lg-7 mb-3 mb-lg-0">
                                    <div class="d-flex align-items-start justify-content-between mb-3">
                                        <h4 class="card-title fw-bold text-dark mb-2">
                                            {{ $activity->title }}
                                        </h4>
                                        <div class="popularity-stars ms-3">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star{{ $activity->popularity >= ($i * 20) ? '' : '-o' }}"></i>
                                            @endfor
                                            <small class="text-muted ms-1">({{ $activity->popularity }})</small>
                                        </div>
                                    </div>

                                    <p class="card-text text-muted mb-3">
                                        {{ Str::limit($activity->description, 150) }}
                                    </p>

                                    <!-- Activity Meta -->
                                    <div class="activity-meta">
                                        <div class="row g-2">
                                            <div class="col-sm-6">
                                                <i class="fas fa-calendar-alt text-primary"></i>
                                                Disponible hasta {{ $activity->end_date->format('d/m/Y') }}
                                            </div>
                                            <div class="col-sm-6">
                                                <i class="fas fa-euro-sign text-success"></i>
                                                {{ $activity->formatted_price }} por persona
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Quick Details Button -->
                                    <div class="mt-3">
                                        <a href="{{ route('activities.show', ['activity' => $activity->id, 'date' => $searchDate, 'people' => $numberOfPeople]) }}" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Ver Detalles Completos
                                        </a>
                                    </div>
                                </div>

                                <!-- Price and Action -->
                                <div class="col-lg-5">
                                    <div class="price-display mb-3">
                                        <div class="small opacity-75 mb-1">Precio Total</div>
                                        <div class="h2 fw-bold mb-1">
                                            {{ number_format($activity->total_price, 2, ',', '.') }} €
                                        </div>
                                        <div class="small opacity-75">
                                            Para {{ $numberOfPeople }} {{ $numberOfPeople == 1 ? 'persona' : 'personas' }}
                                        </div>
                                    </div>

                                    <!-- Purchase Form - REQUERIDO PARA LA PRUEBA -->
                                    <form action="{{ route('bookings.store') }}" method="POST" class="purchase-form">
                                        @csrf
                                        <input type="hidden" name="activity_id" value="{{ $activity->id }}">
                                        <input type="hidden" name="activity_date" value="{{ $searchDate }}">
                                        <input type="hidden" name="number_of_people" value="{{ $numberOfPeople }}">
                                        
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-comprar btn-lg">
                                                <span class="btn-text">
                                                    <i class="fas fa-shopping-cart me-2"></i>
                                                    Comprar Ahora
                                                </span>
                                                <span class="loading d-none">
                                                    <i class="fas fa-spinner fa-spin me-2"></i>
                                                    Procesando...
                                                </span>
                                            </button>
                                        </div>
                                    </form>

                                    <div class="text-center mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-shield-alt text-success me-1"></i>
                                            Booking segura • Confirmación inmediata
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Additional Actions -->
        <div class="row mt-5">
            <div class="col-lg-8 mx-auto text-center">
                <div class="bg-light rounded-3 p-4">
                    <h5 class="mb-3">¿No encuentras lo que buscas?</h5>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="{{ route('home') }}" class="btn btn-outline-primary">
                            <i class="fas fa-search me-2"></i>
                            Hacer Nueva Búsqueda
                        </a>
                        <a href="{{ route('activities.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-th-large me-2"></i>
                            Ver Todas las Activityes
                        </a>
                        <a href="{{ route('contact') }}" class="btn btn-outline-info">
                            <i class="fas fa-headset me-2"></i>
                            Contactar Soporte
                        </a>
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- No Results -->
        <div class="no-results">
            <div class="mb-4">
                <i class="fas fa-search fa-4x text-muted mb-3"></i>
                <h3 class="text-dark">No encontramos Activityes para tu búsqueda</h3>
                <p class="lead text-muted">
                    No hay Activityes disponibles para {{ $numberOfPeople }} {{ $numberOfPeople == 1 ? 'persona' : 'personas' }} 
                    el {{ \Carbon\Carbon::parse($searchDate)->format('d/m/Y') }}
                </p>
            </div>

            <div class="row g-3 justify-content-center">
                <div class="col-sm-auto">
                    <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-search me-2"></i>
                        Probar Otra Fecha
                    </a>
                </div>
                <div class="col-sm-auto">
                    <a href="{{ route('activities.index') }}" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-list me-2"></i>
                        Ver Todas las Activityes
                    </a>
                </div>
            </div>

            <!-- Suggestions -->
            <div class="mt-5 pt-4 border-top">
                <h5 class="text-dark mb-3">Sugerencias:</h5>
                <div class="row g-3 text-start">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-plus text-primary me-3 fa-lg"></i>
                            <div>
                                <strong>Prueba otra fecha</strong>
                                <br>
                                <small class="text-muted">Algunas Activityes pueden no estar disponibles todos los días</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-users text-success me-3 fa-lg"></i>
                            <div>
                                <strong>Ajusta el grupo</strong>
                                <br>
                                <small class="text-muted">Algunas Activityes tienen límites de capacidad</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-phone text-info me-3 fa-lg"></i>
                            <div>
                                <strong>Contáctanos</strong>
                                <br>
                                <small class="text-muted">Te ayudamos a encontrar la Activity perfecta</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Floating Action Button -->
<a href="{{ route('home') }}" class="btn btn-primary modify-search-btn" title="Nueva búsqueda">
    <i class="fas fa-search"></i>
</a>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="text-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Procesando...</span>
        </div>
        <div class="mt-3 h5">Procesando tu Booking...</div>
        <div class="text-muted">Por favor, espera un momento</div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle purchase forms
    const purchaseForms = document.querySelectorAll('.purchase-form');
    
    purchaseForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const btnText = submitBtn.querySelector('.btn-text');
            const loading = submitBtn.querySelector('.loading');
            
            // Show loading state
            btnText.classList.add('d-none');
            loading.classList.remove('d-none');
            submitBtn.disabled = true;
            
            // Show overlay
            document.getElementById('loadingOverlay').classList.add('show');
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });

    // Add entrance animations
    const cards = document.querySelectorAll('.activity-result-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s ease';
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Enhanced card interactions
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Scroll to top functionality for floating button
    let lastScrollTop = 0;
    const floatingBtn = document.querySelector('.modify-search-btn');
    
    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        if (scrollTop > 200) {
            floatingBtn.style.opacity = '1';
            floatingBtn.style.visibility = 'visible';
        } else {
            floatingBtn.style.opacity = '0';
            floatingBtn.style.visibility = 'hidden';
        }
        
        lastScrollTop = scrollTop;
    });

    // Auto-hide loading overlay on page load (fallback)
    window.addEventListener('load', function() {
        setTimeout(() => {
            document.getElementById('loadingOverlay').classList.remove('show');
        }, 500);
    });

    // Handle form errors (if any)
    const formErrors = document.querySelectorAll('.invalid-feedback');
    if (formErrors.length > 0) {
        // Re-enable buttons if there are validation errors
        purchaseForms.forEach(form => {
            const submitBtn = form.querySelector('button[type="submit"]');
            const btnText = submitBtn.querySelector('.btn-text');
            const loading = submitBtn.querySelector('.loading');
            
            btnText.classList.remove('d-none');
            loading.classList.add('d-none');
            submitBtn.disabled = false;
        });
        
        document.getElementById('loadingOverlay').classList.remove('show');
    }

    // Analytics tracking (optional)
    cards.forEach(card => {
        const activityId = card.querySelector('input[name="activity_id"]').value;
        
        card.addEventListener('click', function(e) {
            // Only track if not clicking the purchase button
            if (!e.target.closest('.purchase-form')) {
                console.log('Activity viewed:', activityId);
                // gtag('event', 'view_activity', { activity_id: activityId });
            }
        });
    });
});

// Utility function for showing alerts
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
