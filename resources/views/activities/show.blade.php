@extends('layouts.app')

@section('title', $activity->title . ' - Booking tu experiencia única')
@section('description', $activity->description . ' Disponible para ' . $numberOfPeople . ' personas el ' . \Carbon\Carbon::parse($searchDate)->format('d/m/Y') . '. Booking ahora.')
@section('keywords', 'Activity, ' . $activity->title . ', turismo España, Booking, ' . \Carbon\Carbon::parse($searchDate)->format('Y-m-d'))

@push('styles')
<style>
    .activity-hero {
        background: linear-gradient(135deg, 
            rgba(231, 76, 60, 0.9) 0%, 
            rgba(155, 89, 182, 0.8) 50%, 
            rgba(52, 152, 219, 0.9) 100%),
            url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 600"><defs><radialGradient id="heroGrad"><stop offset="0%" stop-color="%23ffffff20"/><stop offset="100%" stop-color="%2300000020"/></radialGradient></defs><rect width="1200" height="600" fill="url(%23heroGrad)"/><circle cx="200" cy="100" r="3" fill="%23ffffff30"/><circle cx="800" cy="200" r="2" fill="%23ffffff20"/><circle cx="1000" cy="400" r="4" fill="%23ffffff25"/></svg>');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        color: white;
        padding: 4rem 0;
        position: relative;
        overflow: hidden;
    }

    .activity-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="1" fill="%23ffffff15"/><circle cx="80" cy="80" r="2" fill="%23ffffff10"/><circle cx="50" cy="30" r="1.5" fill="%23ffffff20"/><circle cx="30" cy="70" r="1" fill="%23ffffff25"/><circle cx="70" cy="40" r="2.5" fill="%23ffffff15"/></svg>');
        animation: float-bg 20s ease-in-out infinite;
    }

    @keyframes float-bg {
        0%, 100% { transform: translate(0, 0) rotate(0deg); }
        33% { transform: translate(30px, -30px) rotate(120deg); }
        66% { transform: translate(-20px, 20px) rotate(240deg); }
    }

    .popularity-display {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 20px;
        padding: 1.5rem;
    }

    .activity-details-card {
        border: none;
        border-radius: 25px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        position: relative;
        margin-top: -80px;
        z-index: 10;
        background: white;
    }

    .activity-details-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, #e74c3c, #9b59b6, #3498db, #1abc9c, #f39c12);
    }

    .booking-panel {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px;
        padding: 2rem;
        position: sticky;
        top: 100px;
        z-index: 100;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    }

    .booking-panel::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 50%);
        animation: rotate-bg 15s linear infinite;
    }

    @keyframes rotate-bg {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .price-breakdown {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 1.5rem;
        margin: 1.5rem 0;
        backdrop-filter: blur(10px);
    }

    .btn-reserve {
        background: linear-gradient(135deg, #ff6b6b 0%, #feca57 100%);
        border: none;
        border-radius: 25px;
        padding: 15px 30px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: white;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
    }

    .btn-reserve::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        transition: left 0.6s ease;
    }

    .btn-reserve:hover::before {
        left: 100%;
    }

    .btn-reserve:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 15px 40px rgba(255, 107, 107, 0.6);
        color: white;
    }

    .related-activity-card {
        border: none;
        border-radius: 15px;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        overflow: hidden;
        position: relative;
        height: 100%;
    }

    .related-activity-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }

    .related-activity-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(52, 152, 219, 0.1), transparent);
        transition: left 0.5s ease;
    }

    .related-activity-card:hover::before {
        left: 100%;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin: 2rem 0;
    }

    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        border-left: 4px solid;
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-card.popularity { border-left-color: #e74c3c; }
    .stat-card.price { border-left-color: #27ae60; }
    .stat-card.availability { border-left-color: #3498db; }
    .stat-card.bookings { border-left-color: #9b59b6; }

    .description-content {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #2c3e50;
    }

    .section-header {
        position: relative;
        margin: 3rem 0 2rem;
        text-align: center;
    }

    .section-header::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 3px;
        background: linear-gradient(90deg, #e74c3c, #3498db);
        border-radius: 2px;
    }

    .availability-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 20;
    }

    .info-tabs {
        border: none;
        background: transparent;
    }

    .info-tabs .nav-link {
        border: none;
        border-radius: 25px;
        padding: 12px 25px;
        font-weight: 600;
        color: #6c757d;
        transition: all 0.3s ease;
        margin: 0 5px;
    }

    .info-tabs .nav-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    .tab-content {
        padding: 2rem 0;
    }

    .feature-list {
        list-style: none;
        padding: 0;
    }

    .feature-list li {
        padding: 0.5rem 0;
        border-bottom: 1px solid #f8f9fa;
        display: flex;
        align-items: center;
    }

    .feature-list li:last-child {
        border-bottom: none;
    }

    .feature-list li i {
        color: #27ae60;
        margin-right: 1rem;
        width: 20px;
    }

    @media (max-width: 768px) {
        .activity-hero {
            padding: 3rem 0;
            background-attachment: scroll;
        }
        
        .activity-details-card {
            margin-top: -50px;
        }
        
        .booking-panel {
            position: relative;
            top: 0;
            margin-top: 2rem;
        }
        
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .loading-state {
        opacity: 0.6;
        pointer-events: none;
        position: relative;
    }

    .loading-state::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 20px;
        height: 20px;
        border: 2px solid #ffffff;
        border-top: 2px solid transparent;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: translate(-50%, -50%) rotate(0deg); }
        100% { transform: translate(-50%, -50%) rotate(360deg); }
    }

    .fade-in-up {
        animation: fadeInUp 0.8s ease forwards;
        opacity: 0;
        transform: translateY(30px);
    }

    .fade-in-up.delay-1 { animation-delay: 0.1s; }
    .fade-in-up.delay-2 { animation-delay: 0.2s; }
    .fade-in-up.delay-3 { animation-delay: 0.3s; }
    .fade-in-up.delay-4 { animation-delay: 0.4s; }

    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush

@section('content')
<!-- Activity Hero -->
<section class="activity-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="fade-in-up">
                    <!-- Availability Badge -->
                    <div class="availability-badge">
                        <span class="badge bg-success fs-6 px-3 py-2">
                            <i class="fas fa-check-circle me-2"></i>
                            Disponible
                        </span>
                    </div>

                    <h1 class="display-4 fw-bold mb-4">
                        {{ $activity->title }}
                    </h1>
                    
                    <p class="lead mb-4 opacity-90">
                        {{ Str::limit($activity->description, 200) }}
                    </p>

                    <!-- Search Parameters Display -->
                    <div class="d-flex flex-wrap gap-3 mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar text-warning me-2"></i>
                            <span class="fw-semibold">{{ \Carbon\Carbon::parse($searchDate)->format('d/m/Y') }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-users text-info me-2"></i>
                            <span class="fw-semibold">{{ $numberOfPeople }} {{ $numberOfPeople == 1 ? 'Persona' : 'Personas' }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-euro-sign text-success me-2"></i>
                            <span class="fw-semibold">{{ number_format($totalPrice, 2, ',', '.') }} € Total</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="popularity-display text-center fade-in-up delay-1">
                    <h3 class="mb-3">
                        <i class="fas fa-star text-warning me-2"></i>
                        Popularidad
                    </h3>
                    <div class="display-3 fw-bold mb-3">{{ $activity->popularity }}/100</div>
                    <div class="mb-3">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star{{ $activity->popularity >= ($i * 20) ? '' : '-o' }} text-warning fa-lg"></i>
                        @endfor
                    </div>
                    @if($activity->popularity > 90)
                        <span class="badge bg-danger fs-6 px-3 py-2">
                            <i class="fas fa-fire me-1"></i>¡Súper Popular!
                        </span>
                    @elseif($activity->popularity > 70)
                        <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                            <i class="fas fa-thumbs-up me-1"></i>Muy Popular
                        </span>
                    @else
                        <span class="badge bg-primary fs-6 px-3 py-2">
                            <i class="fas fa-heart me-1"></i>Recomendada
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container">
    <!-- Activity Details Card -->
    <div class="activity-details-card">
        <div class="card-body p-0">
            <div class="row g-0">
                <div class="col-lg-8">
                    <div class="p-4 p-lg-5">
                        <!-- Description -->
                        <div class="fade-in-up delay-2">
                            <h2 class="h3 fw-bold mb-4">
                                <i class="fas fa-info-circle text-primary me-2"></i>
                                Descripción de la Activity
                            </h2>
                            <div class="description-content">
                                {{ $activity->description }}
                            </div>
                        </div>

                        <!-- Stats Grid -->
                        <div class="stats-grid fade-in-up delay-3">
                            <div class="stat-card popularity">
                                <i class="fas fa-star text-danger fa-2x mb-2"></i>
                                <h5 class="fw-bold">Popularidad</h5>
                                <div class="h4 mb-0">{{ $activity->popularity }}/100</div>
                            </div>
                            <div class="stat-card price">
                                <i class="fas fa-euro-sign text-success fa-2x mb-2"></i>
                                <h5 class="fw-bold">Precio/Persona</h5>
                                <div class="h4 mb-0">{{ $activity->formatted_price }}</div>
                            </div>
                            <div class="stat-card availability">
                                <i class="fas fa-calendar-check text-primary fa-2x mb-2"></i>
                                <h5 class="fw-bold">Disponible</h5>
                                <div class="h6 mb-0">Hasta {{ $activity->end_date->format('d/m/Y') }}</div>
                            </div>
                            <div class="stat-card bookings">
                                <i class="fas fa-users text-purple fa-2x mb-2"></i>
                                <h5 class="fw-bold">Bookings</h5>
                                <div class="h4 mb-0">{{ $totalBookings ?? 0 }}</div>
                            </div>
                        </div>

                        <!-- Information Tabs -->
                        <div class="fade-in-up delay-4">
                            <ul class="nav nav-pills info-tabs justify-content-center" id="infoTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="details-tab" data-bs-toggle="pill" data-bs-target="#details" type="button" role="tab">
                                        <i class="fas fa-list me-1"></i>Detalles
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="includes-tab" data-bs-toggle="pill" data-bs-target="#includes" type="button" role="tab">
                                        <i class="fas fa-check-circle me-1"></i>Incluye
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="requirements-tab" data-bs-toggle="pill" data-bs-target="#requirements" type="button" role="tab">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Requisitos
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content" id="infoTabsContent">
                                <div class="tab-pane fade show active" id="details" role="tabpanel">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <h6 class="fw-bold">Duración Estimada</h6>
                                            <p class="text-muted">3-4 horas aproximadamente</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="fw-bold">Idiomas Disponibles</h6>
                                            <p class="text-muted">Español, Inglés</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="fw-bold">Punto de Encuentro</h6>
                                            <p class="text-muted">Se confirmará 24h antes</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="fw-bold">Cancelación</h6>
                                            <p class="text-muted">Gratuita hasta 48h antes</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="includes" role="tabpanel">
                                    <ul class="feature-list">
                                        <li><i class="fas fa-check"></i> Guía profesional especializado</li>
                                        <li><i class="fas fa-check"></i> Entrada a los sitios incluidos</li>
                                        <li><i class="fas fa-check"></i> Explicaciones detalladas</li>
                                        <li><i class="fas fa-check"></i> Fotografías del grupo</li>
                                        <li><i class="fas fa-check"></i> Mapa e información adicional</li>
                                        <li><i class="fas fa-check"></i> Asistencia durante toda la Activity</li>
                                    </ul>
                                </div>
                                <div class="tab-pane fade" id="requirements" role="tabpanel">
                                    <ul class="feature-list">
                                        <li><i class="fas fa-id-card"></i> Documento de identidad válido</li>
                                        <li><i class="fas fa-walking"></i> Calzado cómodo recomendado</li>
                                        <li><i class="fas fa-sun"></i> Protección solar (verano)</li>
                                        <li><i class="fas fa-tint"></i> Botella de agua</li>
                                        <li><i class="fas fa-camera"></i> Cámara para capturar momentos</li>
                                        <li><i class="fas fa-clock"></i> Puntualidad en el punto de encuentro</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Panel -->
                <div class="col-lg-4">
                    <div class="p-4 p-lg-3">
                        <div class="booking-panel fade-in-up delay-1">
                            <div class="text-center mb-4">
                                <h3 class="fw-bold">
                                    <i class="fas fa-ticket-alt me-2"></i>
                                    Booking Ahora
                                </h3>
                                <p class="mb-0 opacity-75">¡Asegura tu lugar en esta experiencia única!</p>
                            </div>

                            <!-- Price Breakdown -->
                            <div class="price-breakdown">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ $activity->formatted_price }} × {{ $numberOfPeople }}</span>
                                    <span>{{ number_format($activity->price_per_person * $numberOfPeople, 2, ',', '.') }} €</span>
                                </div>
                                <hr class="border-light opacity-25">
                                <div class="d-flex justify-content-between">
                                    <strong class="h5">Total</strong>
                                    <strong class="h4">{{ number_format($totalPrice, 2, ',', '.') }} €</strong>
                                </div>
                            </div>

                            <!-- Booking Form - REQUERIDO PARA LA PRUEBA -->
                            <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
                                @csrf
                                <input type="hidden" name="activity_id" value="{{ $activity->id }}">
                                <input type="hidden" name="activity_date" value="{{ $searchDate }}">
                                <input type="hidden" name="number_of_people" value="{{ $numberOfPeople }}">
                                
                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-reserve btn-lg">
                                        <span class="btn-text">
                                            <i class="fas fa-shopping-cart me-2"></i>
                                            Comprar - {{ number_format($totalPrice, 2, ',', '.') }} €
                                        </span>
                                        <span class="loading d-none">
                                            <i class="fas fa-spinner fa-spin me-2"></i>
                                            Procesando...
                                        </span>
                                    </button>
                                </div>
                            </form>

                            <div class="text-center">
                                <small class="opacity-75">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Pago seguro • Confirmación inmediata
                                </small>
                            </div>

                            <!-- Quick Info -->
                            <div class="mt-4 pt-3 border-top border-light border-opacity-25">
                                <div class="row g-2 text-center">
                                    <div class="col-6">
                                        <i class="fas fa-calendar text-warning d-block mb-1"></i>
                                        <small>{{ \Carbon\Carbon::parse($searchDate)->format('d/m/Y') }}</small>
                                    </div>
                                    <div class="col-6">
                                        <i class="fas fa-users text-info d-block mb-1"></i>
                                        <small>{{ $numberOfPeople }} {{ $numberOfPeople == 1 ? 'Persona' : 'Personas' }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Activities Section - FUNCIONALIDAD OPCIONAL REQUERIDA -->
    @if($relatedActivities && $relatedActivities->count() > 0)
        <div class="section-header">
            <h2 class="display-6 fw-bold text-dark">
                <i class="fas fa-heart text-danger me-2"></i>
                También te Puede Interesar
            </h2>
            <p class="lead text-muted">Activityes relacionadas disponibles para {{ \Carbon\Carbon::parse($searchDate)->format('d/m/Y') }}</p>
        </div>

        <div class="row g-4 mb-5 fade-in-up delay-2">
            @foreach($relatedActivities as $related)
                <div class="col-lg-4 col-md-6">
                    <div class="card related-activity-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title fw-bold">{{ $related->title }}</h5>
                                <div class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star{{ $related->popularity >= ($i * 20) ? '' : '-o' }} fa-sm"></i>
                                    @endfor
                                </div>
                            </div>
                            
                            <p class="card-text text-muted">{{ Str::limit($related->description, 100) }}</p>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <small class="text-muted d-block">Precio total</small>
                                    <strong class="text-primary h5">{{ number_format($related->total_price, 2, ',', '.') }} €</strong>
                                </div>
                                <span class="badge bg-{{ $related->popularity > 80 ? 'danger' : ($related->popularity > 60 ? 'warning text-dark' : 'primary') }}">
                                    {{ $related->popularity > 80 ? 'Muy Popular' : ($related->popularity > 60 ? 'Popular' : 'Recomendada') }}
                                </span>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <a href="{{ route('activities.show', ['activity' => $related->id, 'date' => $searchDate, 'people' => $numberOfPeople]) }}" 
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i>Ver Detalles
                                </a>
                                <form action="{{ route('bookings.store') }}" method="POST" class="related-booking-form">
                                    @csrf
                                    <input type="hidden" name="activity_id" value="{{ $related->id }}">
                                    <input type="hidden" name="activity_date" value="{{ $searchDate }}">
                                    <input type="hidden" name="number_of_people" value="{{ $numberOfPeople }}">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-shopping-cart me-1"></i>
                                        Bookingr
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Additional Actions -->
    <div class="text-center mb-5 fade-in-up delay-3">
        <div class="bg-light rounded-3 p-4">
            <h5 class="mb-3">¿Buscas algo diferente?</h5>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="{{ route('activities.search') }}" 
                   class="btn btn-outline-primary"
                   onclick="event.preventDefault(); document.getElementById('new-search-form').submit();">
                    <i class="fas fa-search me-2"></i>
                    Nueva Búsqueda
                </a>
                <a href="{{ route('activities.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-th-large me-2"></i>
                    Todas las Activityes
                </a>
                <a href="{{ route('contact') }}" class="btn btn-outline-info">
                    <i class="fas fa-headset me-2"></i>
                    Ayuda Personalizada
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for new search -->
<form id="new-search-form" action="{{ route('activities.search') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="date" value="{{ $searchDate }}">
    <input type="hidden" name="people" value="{{ $numberOfPeople }}">
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Main booking form handling
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const btnText = submitBtn.querySelector('.btn-text');
            const loading = submitBtn.querySelector('.loading');
            
            // Show loading state
            btnText.classList.add('d-none');
            loading.classList.remove('d-none');
            submitBtn.disabled = true;
            
            // Add loading class to booking panel
            document.querySelector('.booking-panel').classList.add('loading-state');
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // Related activities booking forms
    const relatedForms = document.querySelectorAll('.related-booking-form');
    relatedForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Procesando...';
            submitBtn.disabled = true;
            
            // Add loading state to the card
            this.closest('.related-activity-card').classList.add('loading-state');
        });
    });

    // Enhanced scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate');
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe all fade-in elements
    document.querySelectorAll('.fade-in-up').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'all 0.8s ease';
        observer.observe(el);
    });

    // Sticky booking panel enhancement
    const bookingPanel = document.querySelector('.booking-panel');
    if (bookingPanel && window.innerWidth > 992) {
        let lastScrollTop = 0;
        
        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const windowHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight;
            const panelHeight = bookingPanel.offsetHeight;
            
            // Adjust sticky behavior based on scroll direction
            if (scrollTop > lastScrollTop) {
                // Scrolling down
                if (scrollTop + windowHeight > documentHeight - 200) {
                    bookingPanel.style.position = 'absolute';
                    bookingPanel.style.bottom = '20px';
                    bookingPanel.style.top = 'auto';
                }
            } else {
                // Scrolling up
                if (scrollTop < documentHeight - windowHeight - 200) {
                    bookingPanel.style.position = 'sticky';
                    bookingPanel.style.top = '100px';
                    bookingPanel.style.bottom = 'auto';
                }
            }
            
            lastScrollTop = scrollTop;
        });
    }

    // Tab switching enhancement
    const tabButtons = document.querySelectorAll('#infoTabs button');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Add slight delay for better UX
            setTimeout(() => {
                const targetPane = document.querySelector(this.getAttribute('data-bs-target'));
                if (targetPane) {
                    targetPane.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'nearest' 
                    });
                }
            }, 100);
        });
    });

    // Price animation on load
    const priceElements = document.querySelectorAll('.h4, .display-3');
    priceElements.forEach(el => {
        if (el.textContent.includes('€') || el.textContent.includes('/100')) {
            el.style.opacity = '0';
            el.style.transform = 'scale(0.8)';
            
            setTimeout(() => {
                el.style.transition = 'all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
                el.style.opacity = '1';
                el.style.transform = 'scale(1)';
            }, 300);
        }
    });

    // Related activities card interactions
    const relatedCards = document.querySelectorAll('.related-activity-card');
    relatedCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-12px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            if (!this.classList.contains('loading-state')) {
                this.style.transform = 'translateY(0) scale(1)';
            }
        });
    });

    // Booking panel floating effect
    if (bookingPanel) {
        let floatAnimation;
        
        const startFloating = () => {
            let start = null;
            
            const animate = (timestamp) => {
                if (!start) start = timestamp;
                const progress = timestamp - start;
                
                const offset = Math.sin(progress * 0.002) * 3;
                bookingPanel.style.transform = `translateY(${offset}px)`;
                
                floatAnimation = requestAnimationFrame(animate);
            };
            
            floatAnimation = requestAnimationFrame(animate);
        };
        
        const stopFloating = () => {
            if (floatAnimation) {
                cancelAnimationFrame(floatAnimation);
                bookingPanel.style.transform = 'translateY(0)';
            }
        };
        
        // Start floating when not interacting
        let interactionTimeout;
        
        const resetFloating = () => {
            stopFloating();
            clearTimeout(interactionTimeout);
            interactionTimeout = setTimeout(startFloating, 2000);
        };
        
        bookingPanel.addEventListener('mouseenter', stopFloating);
        bookingPanel.addEventListener('mouseleave', resetFloating);
        
        // Start floating initially
        setTimeout(startFloating, 1000);
    }

    // Availability check (optional enhancement)
    const checkAvailability = () => {
        const activityId = document.querySelector('input[name="activity_id"]').value;
        const date = document.querySelector('input[name="activity_date"]').value;
        const people = document.querySelector('input[name="number_of_people"]').value;
        
        // This could make an AJAX call to check real-time availability
        // fetch('/api/check-availability', { ... })
        console.log(`Checking availability for activity ${activityId} on ${date} for ${people} people`);
    };

    // Check availability on load (optional)
    setTimeout(checkAvailability, 1000);

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey || e.metaKey) {
            switch(e.key) {
                case 'b':
                    e.preventDefault();
                    bookingForm.querySelector('button[type="submit"]').click();
                    break;
                case 'h':
                    e.preventDefault();
                    window.location.href = '{{ route("home") }}';
                    break;
                case 's':
                    e.preventDefault();
                    document.getElementById('new-search-form').submit();
                    break;
            }
        }
    });

    // Analytics tracking (optional)
    const trackEvent = (action, label) => {
        if (typeof gtag !== 'undefined') {
            gtag('event', action, {
                'event_category': 'engagement',
                'event_label': label,
                'activity_id': '{{ $activity->id }}'
            });
        }
    };

    // Track page view
    trackEvent('view_activity_detail', '{{ $activity->title }}');

    // Track tab switches
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            trackEvent('tab_click', this.textContent.trim());
        });
    });

    // Track related activity clicks
    document.querySelectorAll('.related-activity-card a, .related-activity-card button').forEach(el => {
        el.addEventListener('click', function() {
            const activityTitle = this.closest('.card').querySelector('.card-title').textContent;
            trackEvent('related_activity_click', activityTitle);
        });
    });

    // Error handling for forms
    const handleFormErrors = () => {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn && submitBtn.disabled) {
                // Re-enable if there are validation errors on page
                if (document.querySelector('.invalid-feedback')) {
                    submitBtn.disabled = false;
                    const btnText = submitBtn.querySelector('.btn-text');
                    const loading = submitBtn.querySelector('.loading');
                    
                    if (btnText && loading) {
                        btnText.classList.remove('d-none');
                        loading.classList.add('d-none');
                    } else {
                        submitBtn.innerHTML = submitBtn.innerHTML.replace(/fa-spinner fa-spin/, 'fa-shopping-cart');
                    }
                }
            }
        });
        
        // Remove loading states from cards
        document.querySelectorAll('.loading-state').forEach(el => {
            el.classList.remove('loading-state');
        });
    };

    // Check for errors on page load
    setTimeout(handleFormErrors, 100);

    // Auto-scroll to booking panel on mobile after reading description
    if (window.innerWidth <= 768) {
        let hasScrolledToBooking = false;
        
        window.addEventListener('scroll', function() {
            const descriptionEnd = document.querySelector('.description-content').offsetTop + 
                                  document.querySelector('.description-content').offsetHeight;
            
            if (!hasScrolledToBooking && window.pageYOffset > descriptionEnd - 100) {
                hasScrolledToBooking = true;
                setTimeout(() => {
                    bookingPanel.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                }, 500);
            }
        });
    }
});

// Utility function for showing custom alerts
function showCustomAlert(message, type = 'info', duration = 5000) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 400px;';
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, duration);
}
</script>
@endpush
