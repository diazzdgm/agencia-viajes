@extends('layouts.app')

@section('title', 'Actividades Disponibles')

@section('content')
<div class="container py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-5 fw-bold text-primary mb-2">
                        <i class="fas fa-compass me-2"></i>
                        Actividades Disponibles
                    </h1>
                    <p class="lead text-muted">
                        Descubre experiencias únicas y emocionantes
                        <span class="badge bg-primary ms-2">{{ $activities->total() ?? $activities->count() }} actividades</span>
                    </p>
                </div>
                <div class="d-none d-md-block">
                    <div class="btn-group" role="group">
                        <a href="{{ route('category', 'popular') }}" class="btn btn-outline-primary">
                            <i class="fas fa-fire me-1"></i> Populares
                        </a>
                        <a href="{{ route('category', 'today') }}" class="btn btn-outline-success">
                            <i class="fas fa-calendar-day me-1"></i> Hoy
                        </a>
                        <a href="{{ route('activities.index') }}" class="btn btn-primary">
                            <i class="fas fa-th-large me-1"></i> Todas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Quick Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body py-3">
                    <form method="GET" action="{{ route('activities.search.get') }}" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-calendar-alt me-1"></i> Fecha
                            </label>
                            <input type="date" 
                                   name="date" 
                                   class="form-control" 
                                   value="{{ request('date', date('Y-m-d')) }}"
                                   min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-users me-1"></i> Personas
                            </label>
                            <select name="people" class="form-select">
                                @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ request('people', 2) == $i ? 'selected' : '' }}>
                                        {{ $i }} {{ $i == 1 ? 'persona' : 'personas' }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-search me-1"></i> Buscar
                            </label>
                            <input type="text" 
                                   name="query" 
                                   class="form-control" 
                                   placeholder="Nombre de actividad..."
                                   value="{{ request('query') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i> Buscar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Activities Grid -->
    @if($activities->count() > 0)
        <div class="row g-4">
            @foreach($activities as $activity)
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm border-0 hover-lift">
                        <!-- Activity Image -->
                        <div class="card-img-top position-relative overflow-hidden" style="height: 220px;">
                            @if($activity->image_url)
                                <img src="{{ $activity->image_url }}" 
                                     class="w-100 h-100 object-fit-cover" 
                                     alt="{{ $activity->title }}">
                            @else
                                <div class="w-100 h-100 bg-gradient-primary d-flex align-items-center justify-content-center">
                                    <i class="fas fa-image text-white opacity-50" style="font-size: 3rem;"></i>
                                </div>
                            @endif
                            
                            <!-- Popularity Badge -->
                            @if($activity->popularity >= 80)
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-danger">
                                        <i class="fas fa-fire me-1"></i> Popular
                                    </span>
                                </div>
                            @endif
                            
                            <!-- Price Badge -->
                            <div class="position-absolute bottom-0 start-0 m-2">
                                <span class="badge bg-dark bg-opacity-75 fs-6">
                                    €{{ number_format($activity->price_per_person, 0) }}
                                    <small>/persona</small>
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold mb-2">
                                {{ $activity->title }}
                            </h5>
                            
                            <p class="card-text text-muted flex-grow-1 mb-3">
                                {{ Str::limit($activity->description, 100) }}
                            </p>
                            
                            <!-- Activity Info -->
                            <div class="mb-3">
                                <div class="row g-2 text-sm">
                                    <div class="col-6">
                                        <i class="fas fa-clock text-primary me-1"></i>
                                        <small class="text-muted">{{ $activity->duration ?? '4 horas' }}</small>
                                    </div>
                                    <div class="col-6">
                                        <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                        <small class="text-muted">{{ $activity->location ?? 'Madrid' }}</small>
                                    </div>
                                    <div class="col-6">
                                        <i class="fas fa-users text-success me-1"></i>
                                        <small class="text-muted">Max {{ $activity->max_people ?? 20 }}</small>
                                    </div>
                                    <div class="col-6">
                                        <i class="fas fa-star text-warning me-1"></i>
                                        <small class="text-muted">{{ number_format($activity->popularity/20, 1) }}/5</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Availability Status -->
                            <div class="mb-3">
                                @if($activity->available_spots > 0)
                                    <div class="alert alert-success alert-sm py-2 mb-0">
                                        <i class="fas fa-check-circle me-1"></i>
                                        <small>{{ $activity->available_spots }} plazas disponibles</small>
                                    </div>
                                @else
                                    <div class="alert alert-warning alert-sm py-2 mb-0">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        <small>Plazas limitadas</small>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="mt-auto">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('activity.show', $activity) }}?date={{ request('date', date('Y-m-d')) }}&people={{ request('people', 2) }}" 
                                       class="btn btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i> Ver Detalles
                                    </a>
                                    <button type="button" 
                                            class="btn btn-primary btn-book-now" 
                                            data-activity-id="{{ $activity->id }}"
                                            data-activity-title="{{ $activity->title }}"
                                            data-activity-price="{{ $activity->price_per_person }}">
                                        <i class="fas fa-shopping-cart me-1"></i> 
                                        Reservar Ahora - €{{ number_format($activity->price_per_person * request('people', 2), 0) }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if(method_exists($activities, 'links'))
            <div class="row mt-5">
                <div class="col-12 d-flex justify-content-center">
                    {{ $activities->withQueryString()->links() }}
                </div>
            </div>
        @endif

    @else
        <!-- No Activities Found -->
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-search text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="text-muted mb-3">No se encontraron actividades</h3>
                    <p class="text-muted mb-4">
                        No hay actividades disponibles que coincidan con tus criterios de búsqueda.
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="fas fa-home me-1"></i> Volver al Inicio
                        </a>
                        <a href="{{ route('activities.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-refresh me-1"></i> Ver Todas las Actividades
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Quick Booking Modal -->
<div class="modal fade" id="quickBookingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-shopping-cart me-2"></i>
                    Reserva Rápida
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickBookingForm" method="POST" action="{{ route('booking.store') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="activity_id" id="modal_activity_id">
                    
                    <div class="mb-3">
                        <h6 id="modal_activity_title" class="fw-bold"></h6>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Fecha</label>
                            <input type="date" 
                                   name="activity_date" 
                                   class="form-control" 
                                   value="{{ request('date', date('Y-m-d')) }}"
                                   min="{{ date('Y-m-d') }}" 
                                   required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Personas</label>
                            <select name="number_of_people" class="form-select" required>
                                @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ request('people', 2) == $i ? 'selected' : '' }}>
                                        {{ $i }} {{ $i == 1 ? 'persona' : 'personas' }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-3 p-3 bg-light rounded">
                        <div class="d-flex justify-content-between">
                            <span>Precio por persona:</span>
                            <span id="modal_price_per_person"></span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total:</span>
                            <span id="modal_total_price"></span>
                        </div>
                    </div>
                    
                    <input type="hidden" name="booking_price" id="modal_booking_price">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check me-1"></i> Confirmar Reserva
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.hover-lift {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
}

.object-fit-cover {
    object-fit: cover;
}

.alert-sm {
    font-size: 0.875rem;
}

.text-sm small {
    font-size: 0.75rem;
}

@media (max-width: 768px) {
    .display-5 {
        font-size: 1.5rem;
    }
    
    .card-img-top {
        height: 180px !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quick booking functionality
    const bookButtons = document.querySelectorAll('.btn-book-now');
    const modal = new bootstrap.Modal(document.getElementById('quickBookingModal'));
    
    bookButtons.forEach(button => {
        button.addEventListener('click', function() {
            const activityId = this.dataset.activityId;
            const activityTitle = this.dataset.activityTitle;
            const activityPrice = parseFloat(this.dataset.activityPrice);
            
            // Update modal content
            document.getElementById('modal_activity_id').value = activityId;
            document.getElementById('modal_activity_title').textContent = activityTitle;
            document.getElementById('modal_price_per_person').textContent = '€' + activityPrice.toFixed(0);
            
            // Calculate initial total
            updateModalTotal();
            
            modal.show();
        });
    });
    
    // Update total price when people number changes
    document.querySelector('#quickBookingModal select[name="number_of_people"]').addEventListener('change', updateModalTotal);
    
    function updateModalTotal() {
        const pricePerPerson = parseFloat(document.querySelector('.btn-book-now:focus, .btn-book-now:hover')?.dataset.activityPrice || 
                                        document.querySelector('.btn-book-now')?.dataset.activityPrice || 0);
        const numberOfPeople = parseInt(document.querySelector('#quickBookingModal select[name="number_of_people"]').value);
        const total = pricePerPerson * numberOfPeople;
        
        document.getElementById('modal_total_price').textContent = '€' + total.toFixed(0);
        document.getElementById('modal_booking_price').value = total;
    }
    
    // Filter functionality
    const searchForm = document.querySelector('form[action*="search"]');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            // Add loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Buscando...';
            submitBtn.disabled = true;
        });
    }
});
</script>
@endpush