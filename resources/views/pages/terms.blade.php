@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5">
                    <h1 class="display-6 fw-bold text-primary mb-4">
                        <i class="fas fa-file-contract me-2"></i>
                        {{ $title }}
                    </h1>
                    
                    <div class="text-muted mb-4">
                        <small>Última actualización: {{ date('d/m/Y') }}</small>
                    </div>
                    
                    <div class="content">
                        <h3>1. Aceptación de los Términos</h3>
                        <p>Al acceder y utilizar este sitio web, aceptas estar sujeto a estos términos y condiciones de uso.</p>
                        
                        <h3>2. Uso del Servicio</h3>
                        <p>Nuestros servicios están destinados a la reserva de actividades turísticas. Te comprometes a usar el servicio de manera legal y apropiada.</p>
                        
                        <h3>3. Reservas y Pagos</h3>
                        <p>Las reservas están sujetas a disponibilidad. Los precios pueden cambiar sin previo aviso hasta la confirmación de la reserva.</p>
                        
                        <h3>4. Cancelaciones</h3>
                        <p>Las cancelaciones deben realizarse con al menos 24 horas de anticipación para obtener un reembolso completo.</p>
                        
                        <h3>5. Limitación de Responsabilidad</h3>
                        <p>No nos hacemos responsables por daños indirectos o consecuenciales derivados del uso de nuestros servicios.</p>
                    </div>
                    
                    <div class="mt-5 pt-4 border-top">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Volver al Inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection