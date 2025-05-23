@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5">
                    <h1 class="display-6 fw-bold text-primary mb-4">
                        <i class="fas fa-shield-alt me-2"></i>
                        {{ $title }}
                    </h1>
                    
                    <div class="text-muted mb-4">
                        <small>Última actualización: {{ date('d/m/Y') }}</small>
                    </div>
                    
                    <div class="content">
                        <h3>1. Información que Recopilamos</h3>
                        <p>Recopilamos información personal como nombre, email y teléfono cuando realizas una reserva.</p>
                        
                        <h3>2. Uso de la Información</h3>
                        <p>Utilizamos tu información para procesar reservas, enviar confirmaciones y mejorar nuestros servicios.</p>
                        
                        <h3>3. Compartir Información</h3>
                        <p>No compartimos tu información personal con terceros sin tu consentimiento, excepto cuando sea necesario para completar tu reserva.</p>
                        
                        <h3>4. Cookies</h3>
                        <p>Utilizamos cookies para mejorar tu experiencia de navegación y analizar el uso del sitio web.</p>
                        
                        <h3>5. Seguridad</h3>
                        <p>Implementamos medidas de seguridad apropiadas para proteger tu información personal.</p>
                        
                        <h3>6. Tus Derechos</h3>
                        <p>Tienes derecho a acceder, corregir o eliminar tu información personal. Contáctanos para ejercer estos derechos.</p>
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