@extends('layouts.app')

@section('title', 'Confirmación de Reserva')

@section('content')
<div class="container py-5">
    <!-- Breadcrumbs -->
    @if(isset($breadcrumbs))
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                @foreach($breadcrumbs as $breadcrumb)
                    @if($breadcrumb['url'])
                        <li class="breadcrumb-item"><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a></li>
                    @else
                        <li class="breadcrumb-item active">{{ $breadcrumb['title'] }}</li>
                    @endif
                @endforeach
            </ol>
        </nav>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-5">
                    <!-- Success Header -->
                    <div class="text-center mb-5">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h1 class="display-5 fw-bold text-success mb-3">¡Reserva Confirmada!</h1>
                        <p class="lead text-muted">Tu reserva ha sido procesada exitosamente</p>
                    </div>

                    <!-- Booking Details -->
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card bg-light h-100">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">
                                        <i class="fas fa-ticket-alt me-2"></i>Detalles de la Reserva
                                    </h5>
                                    <hr>
                                    <p class="mb-2">
                                        <strong>ID de Reserva:</strong> 
                                        <span class="badge bg-primary">#{{ $booking->id }}</span>
                                    </p>
                                    <p class="mb-2">
                                        <strong>Fecha de Reserva:</strong><br>
                                        <small class="text-muted">{{ $booking->created_at->format('d/m/Y H:i') }}</small>
                                    </p>
                                    <p class="mb-2">
                                        <strong>Estado:</strong>
                                        <span class="badge bg-success">Confirmada</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card bg-light h-100">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">
                                        <i class="fas fa-map-marker-alt me-2"></i>Información de la Actividad
                                    </h5>
                                    <hr>
                                    <p class="mb-2">
                                        <strong>Actividad:</strong><br>
                                        {{ $booking->activity->title ?? 'N/A' }}
                                    </p>
                                    <p class="mb-2">
                                        <strong>Fecha de la Actividad:</strong><br>
                                        {{ \Carbon\Carbon::parse($booking->activity_date)->format('d/m/Y') }}
                                    </p>
                                    <p class="mb-2">
                                        <strong>Número de Personas:</strong>
                                        <span class="badge bg-info">{{ $booking->number_of_people }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Price Summary -->
                    <div class="card mt-4 border-primary">
                        <div class="card-body text-center">
                            <h5 class="card-title text-primary">
                                <i class="fas fa-euro-sign me-2"></i>Resumen de Precios
                            </h5>
                            <hr>
                            <div class="row">
                                <div class="col-sm-6">
                                    <p class="mb-1">Precio por persona:</p>
                                    <h6>€{{ number_format($booking->activity->price_per_person ?? 0, 2) }}</h6>
                                </div>
                                <div class="col-sm-6">
                                    <p class="mb-1">Total pagado:</p>
                                    <h4 class="text-success fw-bold">€{{ number_format($booking->booking_price, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="text-center mt-5">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="{{ route('home') }}" class="btn btn-primary btn-lg me-md-2">
                                <i class="fas fa-home me-2"></i>Volver al Inicio
                            </a>
                            <a href="{{ route('activities.index') }}" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-search me-2"></i>Más Actividades
                            </a>
                        </div>

                        <!-- Additional Info -->
                        <div class="mt-4 pt-4 border-top">
                            <p class="text-muted small">
                                <i class="fas fa-info-circle me-1"></i>
                                Recibirás un email de confirmación en breve. 
                                Guarda este número de reserva para futuras consultas.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

@media print {
    .btn, .breadcrumb {
        display: none !important;
    }
}
</style>
@endpush