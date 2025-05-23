<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SearchController extends Controller
{
    /**
     * Mostrar la página principal con el formulario de búsqueda
     */
    public function index()
    {
        return view('search.index');
    }

    /**
     * Procesar la búsqueda de Activityes
     */
    public function search(Request $request)
    {
        // Validar los datos del formulario
        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'people' => 'required|integer|min:1|max:20'
        ], [
            'date.required' => 'La fecha es obligatoria.',
            'date.date' => 'La fecha debe ser válida.',
            'date.after_or_equal' => 'La fecha debe ser hoy o posterior.',
            'people.required' => 'El número de personas es obligatorio.',
            'people.integer' => 'El número de personas debe ser un número entero.',
            'people.min' => 'Debe ser al menos 1 persona.',
            'people.max' => 'Máximo 20 personas por Booking.'
        ]);

        $searchDate = $validated['date'];
        $numberOfPeople = $validated['people'];

        // Buscar Activityes disponibles en la fecha especificada
        $activities = Activity::availableOnDate($searchDate)
            ->orderByPopularity('desc')
            ->get();

        // Calcular precio total para cada Activity
        $activities->each(function ($activity) use ($numberOfPeople) {
            $activity->total_price = $activity->calculateTotalPrice($numberOfPeople);
        });

        return view('search.results', compact('activities', 'searchDate', 'numberOfPeople'));
    }

    /**
     * API endpoint para búsqueda (opcional para AJAX)
     */
    public function searchApi(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'people' => 'required|integer|min:1|max:20'
        ]);

        $activities = Activity::availableOnDate($validated['date'])
            ->orderByPopularity('desc')
            ->get()
            ->map(function ($activity) use ($validated) {
                return [
                    'id' => $activity->id,
                    'title' => $activity->title,
                    'description' => $activity->short_description,
                    'price_per_person' => $activity->price_per_person,
                    'total_price' => $activity->calculateTotalPrice($validated['people']),
                    'popularity' => $activity->popularity,
                    'formatted_price' => $activity->formatted_price
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $activities,
            'search_params' => [
                'date' => $validated['date'],
                'people' => $validated['people']
            ]
        ]);
    }
}
