<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\BookingController;

/*
|--------------------------------------------------------------------------
| Web Routes - VERSIÓN SIMPLIFICADA PARA TESTING
|--------------------------------------------------------------------------
|
| Rutas básicas y funcionales para el sistema de reservas de actividades
|
*/

// =============================================================================
// RUTAS PRINCIPALES - REQUERIDAS PARA LA PRUEBA TÉCNICA
// =============================================================================

/**
 * Página Principal con Formulario de Búsqueda
 */
Route::get('/', [HomeController::class, 'index'])->name('home');

/**
 * Procesar Búsqueda de Actividades
 */
Route::post('/search', [SearchController::class, 'search'])->name('activities.search');
Route::get('/search', [SearchController::class, 'search'])->name('activities.search.get');

/**
 * Detalle de Actividad
 */
Route::get('/activity/{activity}', [ActivityController::class, 'show'])->name('activities.show');

/**
 * Procesar Reserva
 */
Route::post('/booking', [BookingController::class, 'store'])->name('bookings.store');

/**
 * Confirmación de Reserva
 */
Route::get('/booking/{booking}/confirmation', [BookingController::class, 'confirmation'])->name('bookings.confirmation');

// =============================================================================
// RUTAS DE ACTIVIDADES
// =============================================================================

/**
 * Listado de Actividades
 */
Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');

/**
 * Actividades por Categoría
 */
Route::get('/category/{category}', function($category) {
    $controller = new ActivityController();
    
    switch($category) {
        case 'popular':
            return $controller->popular();
        case 'today': 
            return $controller->today();
        case 'cheap':
            return $controller->cheap();
        default:
            return redirect()->route('activities.index');
    }
})->name('home.category');

// =============================================================================
// PÁGINAS ESTÁTICAS SIMPLES
// =============================================================================

/**
 * Página Acerca de
 */
Route::get('/about', function () {
    return view('pages.about', [
        'title' => 'Acerca de Nosotros',
        'content' => 'Somos una agencia de viajes especializada en experiencias únicas.'
    ]);
})->name('about');

/**
 * Página de Contacto
 */
Route::get('/contact', function () {
    return view('pages.contact', [
        'title' => 'Contacto',
        'content' => 'Ponte en contacto con nosotros para cualquier consulta.'
    ]);
})->name('contact');

/**
 * Términos y Condiciones
 */
Route::get('/terms', function () {
    return view('pages.terms', [
        'title' => 'Términos y Condiciones',
        'content' => 'Términos y condiciones de uso de nuestros servicios.'
    ]);
})->name('terms');

/**
 * Política de Privacidad
 */
Route::get('/privacy', function () {
    return view('pages.privacy', [
        'title' => 'Política de Privacidad', 
        'content' => 'Información sobre el tratamiento de datos personales.'
    ]);
})->name('privacy');

// =============================================================================
// RUTAS DE ACCESO RÁPIDO
// =============================================================================

/**
 * Actividades Populares
 */
Route::get('/popular', function () {
    return redirect()->route('home.category', 'popular');
})->name('activities.popular');

/**
 * Actividades de Hoy
 */
Route::get('/today', function () {
    return redirect()->route('home.category', 'today');
})->name('activities.today');

// =============================================================================
// APIS BÁSICAS (OPCIONAL)
// =============================================================================

Route::prefix('api')->name('api.')->group(function () {
    
    /**
     * API de Búsqueda
     */
    Route::post('/search', [SearchController::class, 'search'])->name('search');
    
    /**
     * Datos de Home
     */
    Route::get('/home', function() {
        return response()->json([
            'activities' => \App\Models\Activity::take(6)->get(),
            'popular' => \App\Models\Activity::popular()->take(3)->get()
        ]);
    })->name('home');
    
    /**
     * Sugerencias
     */
    Route::get('/suggestions', function() {
        return response()->json([
            'suggestions' => \App\Models\Activity::inRandomOrder()->take(5)->get(['title', 'id'])
        ]);
    })->name('suggestions');
});

// =============================================================================
// RUTA DE TESTING
// =============================================================================

Route::get('/test-system', function () {
    return response()->json([
        'status' => 'OK',
        'activities_count' => \App\Models\Activity::count(),
        'bookings_count' => \App\Models\Booking::count(),
        'database_connected' => true,
        'routes_working' => true
    ]);
})->name('test.system');

// =============================================================================
// FALLBACK PARA 404
// =============================================================================

Route::fallback(function () {
    return redirect()->route('home')->with('message', 'Página no encontrada, redirigido al inicio.');
});