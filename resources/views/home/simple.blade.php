@extends('layouts.app')

@section('title', 'Agencia de Viajes - Encuentra tu Activity Perfecta')
@section('description', 'Busca y Booking las mejores Activityes turísticas de España. Experiencias únicas te esperan.')

@push('styles')
<style>
    .simple-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 6rem 0;
        min-height: 70vh;
        display: flex;
        align-items: center;
    }

    .search-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 25px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .error-notice {
        background: rgba(255, 193, 7, 0.1);
        border: 1px solid rgba(255, 193, 7, 0.3);
        border-radius: 15px;
        padding: 1rem;
        margin-bottom: 2rem;
    }
</style>
@endpush

@section('content')
<!-- Simple Hero Section -->
<section class="simple-hero">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h1 class="display-4 fw-bold mb-4">
                        <i class="fas fa-map-marked-alt me-3"></i>
                        Descubre España
                    </h1>
                    <p class="lead">
                        Encuentra las mejores Activityes turísticas y vive experiencias únicas.
                    </p>
                </div>

                <!-- Error Notice (opcional) -->
                @if(session('system_message'))
                    <div class="error-notice text-center">
                        <i class="fas fa-info-circle text-warning me-2"></i>
                        <small class="text-muted">
                            Cargando en modo simplificado. Todas las funciones están disponibles.
                        </small>
                    </div>
                @endif

                <!-- Search Form Card -->
                <div class="search-card p-5">
                    <div class="text-center mb-4">
                        <h3 class="text-dark mb-3">
                            <i class="fas fa-search text-primary me-2"></i>
                            Busca tu Activity Perfecta
                        </h3>
                        <p class="text-muted">Selecciona la fecha y número de personas para comenzar</p>
                    </div>

                    <!-- Search Form -->
                    <form action="{{ route('activities.search') }}" method="POST" id="searchForm">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="date" class="form-label text-dark fw-semibold">
                                    <i class="fas fa-calendar text-primary me-2"></i>
                                    Fecha de la Activity
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
                                    <i class="fas fa-users text-primary me-2"></i>
                                    Número de Personas
                                </label>
                                <select class="form-select form-select-lg @error('people') is-invalid @enderror" 
                                        id="people" 
                                        name="people" 
                                        required>
                                    @for($i = 1; $i <= 20; $i++)
                                        <option value="{{ $i }}" {{ old('people', 2) == $i ? 'selected' : '' }}>
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
                            <button type="submit" class="btn btn-primary btn-lg">
                                <span class="btn-text">
                                    <i class="fas fa-search me-2"></i>
                                    Buscar Activityes Disponibles
                                </span>
                                <span class="loading d-none">
                                    <i class="fas fa-spinner fa-spin me-2"></i>
                                    Buscando...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Quick Links -->
                <div class="text-center mt-4">
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="{{ route('activities.index') }}" class="btn btn-outline-light">
                            <i class="fas fa-th-large me-1"></i>
                            Ver Todas las Activityes
                        </a>
                        <a href="{{ route('home.category', 'popular') }}" class="btn btn-outline-light">
                            <i class="fas fa-star me-1"></i>
                            Más Populares
                        </a>
                        <a href="{{ route('home.category', 'today') }}" class="btn btn-outline-light">
                            <i class="fas fa-calendar-day me-1"></i>
                            Disponibles Hoy
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Simple Features Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 text-center">
                <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                <h5>Booking Segura</h5>
                <p class="text-muted">Proceso de Booking 100% seguro</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fas fa-clock fa-3x text-success mb-3"></i>
                <h5>Confirmación Inmediata</h5>
                <p class="text-muted">Recibe tu confirmación al instante</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fas fa-users fa-3x text-info mb-3"></i>
                <h5>Guías Expertos</h5>
                <p class="text-muted">Profesionales especializados</p>
            </div>
            <div class="col-md-3 text-center">
                <i class="fas fa-heart fa-3x text-danger mb-3"></i>
                <h5>Experiencias Únicas</h5>
                <p class="text-muted">Momentos inolvidables</p>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form handling
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const btnText = submitBtn.querySelector('.btn-text');
            const loading = submitBtn.querySelector('.loading');
            
            btnText.classList.add('d-none');
            loading.classList.remove('d-none');
            submitBtn.disabled = true;
        });
    }

    // Set minimum date to today
    const dateInput = document.getElementById('date');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        if (dateInput.value < today) {
            dateInput.value = today;
        }
    }

    // Simple form validation
    const form = document.getElementById('searchForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const date = document.getElementById('date').value;
            const people = document.getElementById('people').value;
            
            if (!date || !people) {
                e.preventDefault();
                alert('Por favor, completa todos los campos.');
                return false;
            }
            
            const selectedDate = new Date(date);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                e.preventDefault();
                alert('La fecha debe ser hoy o posterior.');
                return false;
            }
        });
    }
});
</script>
@endpush
