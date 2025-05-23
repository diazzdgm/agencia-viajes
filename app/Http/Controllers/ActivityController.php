<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ActivityController extends Controller
{
    /**
     * Mostrar listado de todas las Activityes (opcional)
     */
    public function index()
    {
        $activities = Activity::orderByPopularity('desc')
            ->currentlyAvailable()
            ->paginate(12);

        return view('activities.index', compact('activities'));
    }

    /**
     * Mostrar detalle de una Activity específica
     */
    public function show(Activity $activity, Request $request)
    {
        // Obtener parámetros de búsqueda si vienen de una búsqueda
        $searchDate = $request->input('date', Carbon::today()->format('Y-m-d'));
        $numberOfPeople = $request->input('people', 1);

        // Validar los parámetros
        $request->validate([
            'date' => 'nullable|date|after_or_equal:today',
            'people' => 'nullable|integer|min:1|max:20'
        ]);

        // Validar que la Activity esté disponible en la fecha solicitada
        if (!$activity->isAvailableOnDate($searchDate)) {
            return redirect()->route('home')
                ->with('error', 'La Activity no está disponible en la fecha seleccionada.');
        }

        // Calcular precio total
        $totalPrice = $activity->calculateTotalPrice($numberOfPeople);

        // Obtener Activityes relacionadas disponibles en la fecha
        $relatedActivities = $activity->getRelatedActivitiesForDate($searchDate);

        // Calcular precios para Activityes relacionadas
        $relatedActivities->each(function ($relatedActivity) use ($numberOfPeople) {
            $relatedActivity->total_price = $relatedActivity->calculateTotalPrice($numberOfPeople);
        });

        // Obtener estadísticas de la Activity (opcional para mostrar popularidad)
        $totalBookings = $activity->total_bookings;
        $averageGroupSize = $activity->bookings()->avg('number_of_people') ?? 0;

        // Obtener algunas Bookings recientes (opcional)
        $recentBookings = $activity->bookings()
            ->recent(30)
            ->orderByDesc('booking_date')
            ->limit(5)
            ->get();

        return view('activities.show', compact(
            'activity',
            'searchDate',
            'numberOfPeople',
            'totalPrice',
            'relatedActivities',
            'totalBookings',
            'averageGroupSize',
            'recentBookings'
        ));
    }

    /**
     * Mostrar Activityes populares
     */
    public function popular()
    {
        $activities = Activity::popular(80)
            ->currentlyAvailable()
            ->orderByPopularity('desc')
            ->paginate(12);

        return view('activities.popular', compact('activities'));
    }

    /**
     * Buscar Activityes por título (para autocompletado)
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        if (empty($query)) {
            return response()->json([]);
        }

        $activities = Activity::searchByTitle($query)
            ->currentlyAvailable()
            ->orderByPopularity('desc')
            ->limit(10)
            ->get(['id', 'title', 'price_per_person', 'popularity']);

        return response()->json($activities);
    }

    /**
     * API endpoint para obtener detalles de Activity
     */
    public function apiShow(Activity $activity, Request $request)
    {
        $searchDate = $request->input('date', Carbon::today()->format('Y-m-d'));
        $numberOfPeople = $request->input('people', 1);

        // Validar parámetros
        $request->validate([
            'date' => 'nullable|date|after_or_equal:today',
            'people' => 'nullable|integer|min:1|max:20'
        ]);

        if (!$activity->isAvailableOnDate($searchDate)) {
            return response()->json([
                'success' => false,
                'message' => 'La Activity no está disponible en la fecha seleccionada.'
            ], 404);
        }

        $relatedActivities = $activity->getRelatedActivitiesForDate($searchDate);

        return response()->json([
            'success' => true,
            'data' => [
                'activity' => [
                    'id' => $activity->id,
                    'title' => $activity->title,
                    'description' => $activity->description,
                    'price_per_person' => $activity->price_per_person,
                    'total_price' => $activity->calculateTotalPrice($numberOfPeople),
                    'popularity' => $activity->popularity,
                    'formatted_price' => $activity->formatted_price,
                    'start_date' => $activity->start_date->format('Y-m-d'),
                    'end_date' => $activity->end_date->format('Y-m-d'),
                ],
                'search_params' => [
                    'date' => $searchDate,
                    'people' => $numberOfPeople
                ],
                'related_activities' => $relatedActivities->map(function ($related) use ($numberOfPeople) {
                    return [
                        'id' => $related->id,
                        'title' => $related->title,
                        'price_per_person' => $related->price_per_person,
                        'total_price' => $related->calculateTotalPrice($numberOfPeople),
                        'popularity' => $related->popularity
                    ];
                }),
                'stats' => [
                    'total_bookings' => $activity->total_bookings,
                    'total_revenue' => $activity->total_revenue,
                    'average_group_size' => round($activity->bookings()->avg('number_of_people') ?? 0, 1)
                ]
            ]
        ]);
    }

    /**
     * Obtener Activityes por rango de fechas
     */
    public function getByDateRange(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'people' => 'nullable|integer|min:1|max:20'
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $numberOfPeople = $request->input('people', 1);

        $activities = Activity::where(function ($query) use ($startDate, $endDate) {
                $query->where('start_date', '<=', $endDate)
                      ->where('end_date', '>=', $startDate);
            })
            ->orderByPopularity('desc')
            ->get()
            ->map(function ($activity) use ($numberOfPeople) {
                return [
                    'id' => $activity->id,
                    'title' => $activity->title,
                    'description' => $activity->short_description,
                    'price_per_person' => $activity->price_per_person,
                    'total_price' => $activity->calculateTotalPrice($numberOfPeople),
                    'popularity' => $activity->popularity,
                    'start_date' => $activity->start_date->format('Y-m-d'),
                    'end_date' => $activity->end_date->format('Y-m-d')
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $activities,
            'search_params' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'people' => $numberOfPeople
            ]
        ]);
    }

    /**
     * Obtener estadísticas de una Activity
     */
    public function stats(Activity $activity)
    {
        $stats = [
            'total_bookings' => $activity->total_bookings,
            'total_people_booked' => $activity->total_people_booked,
            'total_revenue' => $activity->total_revenue,
            'average_group_size' => round($activity->bookings()->avg('number_of_people') ?? 0, 1),
            'bookings_last_30_days' => $activity->bookings()->recent(30)->count(),
            'most_popular_dates' => $activity->bookings()
                ->selectRaw('activity_date, COUNT(*) as booking_count')
                ->groupBy('activity_date')
                ->orderByDesc('booking_count')
                ->limit(5)
                ->get(),
            'average_booking_value' => round($activity->bookings()->avg('booking_price') ?? 0, 2)
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Verificar disponibilidad de una Activity
     */
    public function checkAvailability(Activity $activity, Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'people' => 'nullable|integer|min:1|max:20'
        ]);

        $date = $request->input('date');
        $people = $request->input('people', 1);

        $isAvailable = $activity->isAvailableOnDate($date);
        $hasCapacity = $activity->hasAvailabilityForDate($date, $people);

        return response()->json([
            'success' => true,
            'data' => [
                'available' => $isAvailable && $hasCapacity,
                'date' => $date,
                'people' => $people,
                'price_per_person' => $activity->price_per_person,
                'total_price' => $activity->calculateTotalPrice($people),
                'bookings_for_date' => $activity->getPeopleBookedForDate($date)
            ]
        ]);
    }
}
