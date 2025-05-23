<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Mostrar la página principal con formulario de búsqueda
     * y algunas estadísticas/Activityes destacadas
     */
    public function index()
    {
        try {
            // Obtener Activityes más populares para mostrar como destacadas
            $featuredActivities = Activity::popular(80)
                ->currentlyAvailable()
                ->orderByPopularity('desc')
                ->limit(6)
                ->get();

            // Calcular precios para 2 personas (valor por defecto para mostrar)
            $defaultPeople = 2;
            $featuredActivities->each(function ($activity) use ($defaultPeople) {
                $activity->sample_total_price = $activity->calculateTotalPrice($defaultPeople);
            });

            // Estadísticas básicas para mostrar en la página principal
            $stats = [
                'total_activities' => Activity::currentlyAvailable()->count(),
                'popular_activities' => Activity::popular(70)->currentlyAvailable()->count(),
                'total_bookings_today' => Booking::today()->count(),
                'total_people_today' => Booking::today()->sum('number_of_people')
            ];

            // Activityes disponibles hoy (para sugerencias rápidas)
            $availableToday = Activity::availableOnDate(Carbon::today())
                ->orderByPopularity('desc')
                ->limit(3)
                ->get();

            $availableToday->each(function ($activity) use ($defaultPeople) {
                $activity->sample_total_price = $activity->calculateTotalPrice($defaultPeople);
            });

            return view('home.index', compact(
                'featuredActivities',
                'stats',
                'availableToday',
                'defaultPeople'
            ));

        } catch (\Exception $e) {
            // Si hay error, mostrar página simple con solo el formulario
            return view('home.simple');
        }
    }

    /**
     * Página de bienvenida alternativa (más simple)
     */
    public function welcome()
    {
        return view('welcome');
    }

    /**
     * Mostrar página "Acerca de"
     */
    public function about()
    {
        $breadcrumbs = $this->generateBreadcrumbs([
            ['title' => 'Acerca de', 'url' => null]
        ]);

        return view('home.about', compact('breadcrumbs'));
    }

    /**
     * Mostrar página de contacto
     */
    public function contact()
    {
        $breadcrumbs = $this->generateBreadcrumbs([
            ['title' => 'Contacto', 'url' => null]
        ]);

        return view('home.contact', compact('breadcrumbs'));
    }

    /**
     * Procesar formulario de contacto
     */
    public function sendContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:1000'
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'Debe ser un email válido.',
            'subject.required' => 'El asunto es obligatorio.',
            'message.required' => 'El mensaje es obligatorio.'
        ]);

        try {
            // Aquí podrías enviar el email o guardar en BD
            // Mail::to('info@agencia.com')->send(new ContactMail($validated));
            
            if ($this->isAjaxRequest($request)) {
                return $this->successResponse(null, 'Mensaje enviado correctamente. Te contactaremos pronto.');
            }

            return redirect()->route('contact')
                ->with('success', 'Mensaje enviado correctamente. Te contactaremos pronto.');

        } catch (\Exception $e) {
            return $this->handleException($e, 'Error al enviar el mensaje.');
        }
    }

    /**
     * API endpoint para obtener Activityes destacadas
     */
    public function apiHome()
    {
        try {
            $featuredActivities = Activity::popular(80)
                ->currentlyAvailable()
                ->orderByPopularity('desc')
                ->limit(8)
                ->get(['id', 'title', 'description', 'price_per_person', 'popularity']);

            $availableToday = Activity::availableOnDate(Carbon::today())
                ->orderByPopularity('desc')
                ->limit(5)
                ->get(['id', 'title', 'price_per_person', 'popularity']);

            $stats = [
                'total_activities' => Activity::currentlyAvailable()->count(),
                'popular_activities' => Activity::popular(70)->currentlyAvailable()->count(),
                'bookings_today' => Booking::today()->count(),
                'people_today' => Booking::today()->sum('number_of_people')
            ];

            return $this->successResponse([
                'featured_activities' => $featuredActivities,
                'available_today' => $availableToday,
                'stats' => $stats,
                'current_date' => Carbon::today()->format('Y-m-d'),
                'formatted_date' => Carbon::today()->format('d/m/Y')
            ], 'Datos de página principal obtenidos correctamente.');

        } catch (\Exception $e) {
            return $this->handleException($e, 'Error al obtener datos de la página principal.');
        }
    }

    /**
     * Búsqueda rápida desde la página principal
     */
    public function quickSearch(Request $request)
    {
        $validated = $request->validate([
            'date' => 'nullable|date|after_or_equal:today',
            'people' => 'nullable|integer|min:1|max:20'
        ]);

        $searchDate = $validated['date'] ?? Carbon::today()->format('Y-m-d');
        $numberOfPeople = $validated['people'] ?? 2;

        // Redirigir al controlador de búsqueda con parámetros
        return redirect()->route('activities.search', [
            'date' => $searchDate,
            'people' => $numberOfPeople
        ]);
    }

    /**
     * Obtener sugerencias de Activityes basadas en popularidad
     */
    public function suggestions(Request $request)
    {
        try {
            $limit = min($request->input('limit', 6), 20);
            $minPopularity = $request->input('min_popularity', 60);

            $suggestions = Activity::popular($minPopularity)
                ->currentlyAvailable()
                ->orderByPopularity('desc')
                ->limit($limit)
                ->get()
                ->map(function ($activity) {
                    return [
                        'id' => $activity->id,
                        'title' => $activity->title,
                        'short_description' => $activity->short_description,
                        'price_per_person' => $activity->price_per_person,
                        'formatted_price' => $activity->formatted_price,
                        'popularity' => $activity->popularity,
                        'url' => route('activities.show', $activity->id)
                    ];
                });

            return $this->successResponse($suggestions, 'Sugerencias obtenidas correctamente.');

        } catch (\Exception $e) {
            return $this->handleException($e, 'Error al obtener sugerencias.');
        }
    }

    /**
     * Mostrar políticas y términos
     */
    public function terms()
    {
        $breadcrumbs = $this->generateBreadcrumbs([
            ['title' => 'Términos y Condiciones', 'url' => null]
        ]);

        return view('home.terms', compact('breadcrumbs'));
    }

    /**
     * Mostrar política de privacidad
     */
    public function privacy()
    {
        $breadcrumbs = $this->generateBreadcrumbs([
            ['title' => 'Política de Privacidad', 'url' => null]
        ]);

        return view('home.privacy', compact('breadcrumbs'));
    }

    /**
     * Obtener Activityes por categoría (si decides implementar categorías)
     */
    public function byCategory(Request $request)
    {
        $category = $request->input('category', 'popular');
        
        $query = Activity::currentlyAvailable();

        switch ($category) {
            case 'popular':
                $query->popular(70)->orderByPopularity('desc');
                $title = 'Activityes Populares';
                break;
            case 'cheap':
                $query->maxPrice(50)->orderBy('price_per_person');
                $title = 'Activityes Económicas';
                break;
            case 'premium':
                $query->minPrice(100)->orderByPopularity('desc');
                $title = 'Activityes Premium';
                break;
            case 'today':
                $query->availableOnDate(Carbon::today())->orderByPopularity('desc');
                $title = 'Disponibles Hoy';
                break;
            default:
                $query->orderByPopularity('desc');
                $title = 'Todas las Activityes';
        }

        $activities = $query->limit(12)->get();
        
        // Calcular precios de muestra
        $defaultPeople = 2;
        $activities->each(function ($activity) use ($defaultPeople) {
            $activity->sample_total_price = $activity->calculateTotalPrice($defaultPeople);
        });

        if ($this->isAjaxRequest($request)) {
            return $this->successResponse([
                'activities' => $activities,
                'category' => $category,
                'title' => $title,
                'count' => $activities->count()
            ], "Activityes de categoría '{$title}' obtenidas correctamente.");
        }

        $breadcrumbs = $this->generateBreadcrumbs([
            ['title' => $title, 'url' => null]
        ]);

        return view('home.category', compact('activities', 'title', 'category', 'breadcrumbs', 'defaultPeople'));
    }
}
