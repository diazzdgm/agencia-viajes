<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Carbon\Carbon;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Respuesta JSON exitosa estándar
     */
    protected function successResponse($data = null, $message = 'Operación exitosa', $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Respuesta JSON de error estándar
     */
    protected function errorResponse($message = 'Error en la operación', $errors = null, $code = 400)
    {
        $response = [
            'success' => false,
            'message' => $message
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Respuesta de validación fallida
     */
    protected function validationErrorResponse($validator)
    {
        return $this->errorResponse(
            'Los datos proporcionados no son válidos.',
            $validator->errors(),
            422
        );
    }

    /**
     * Validar parámetros comunes de búsqueda de Activityes
     */
    protected function validateSearchParams(Request $request)
    {
        return $request->validate([
            'date' => 'nullable|date|after_or_equal:today',
            'people' => 'nullable|integer|min:1|max:20',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'popularity' => 'nullable|integer|min:1|max:100'
        ], [
            'date.date' => 'La fecha debe ser válida.',
            'date.after_or_equal' => 'La fecha debe ser hoy o posterior.',
            'people.integer' => 'El número de personas debe ser un número entero.',
            'people.min' => 'Debe ser al menos 1 persona.',
            'people.max' => 'Máximo 20 personas por Booking.',
            'min_price.numeric' => 'El precio mínimo debe ser un número.',
            'min_price.min' => 'El precio mínimo no puede ser negativo.',
            'max_price.numeric' => 'El precio máximo debe ser un número.',
            'max_price.min' => 'El precio máximo no puede ser negativo.',
            'popularity.integer' => 'La popularidad debe ser un número entero.',
            'popularity.min' => 'La popularidad mínima es 1.',
            'popularity.max' => 'La popularidad máxima es 100.'
        ]);
    }

    /**
     * Validar parámetros de Booking
     */
    protected function validateBookingParams(Request $request)
    {
        return $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'number_of_people' => 'required|integer|min:1|max:20',
            'activity_date' => 'required|date|after_or_equal:today'
        ], [
            'activity_id.required' => 'Debe seleccionar una Activity.',
            'activity_id.exists' => 'La Activity seleccionada no existe.',
            'number_of_people.required' => 'El número de personas es obligatorio.',
            'number_of_people.integer' => 'El número de personas debe ser un número entero.',
            'number_of_people.min' => 'Debe ser al menos 1 persona.',
            'number_of_people.max' => 'Máximo 20 personas por Booking.',
            'activity_date.required' => 'La fecha de la Activity es obligatoria.',
            'activity_date.date' => 'La fecha debe ser válida.',
            'activity_date.after_or_equal' => 'La fecha debe ser hoy o posterior.'
        ]);
    }

    /**
     * Formatear fecha para mostrar
     */
    protected function formatDate($date, $format = 'd/m/Y')
    {
        if (!$date) {
            return null;
        }

        return Carbon::parse($date)->format($format);
    }

    /**
     * Formatear precio para mostrar
     */
    protected function formatPrice($price)
    {
        return number_format($price, 2, ',', '.') . ' €';
    }

    /**
     * Obtener parámetros de paginación
     */
    protected function getPaginationParams(Request $request)
    {
        return [
            'per_page' => min($request->input('per_page', 15), 50), // máximo 50 elementos por página
            'page' => $request->input('page', 1)
        ];
    }

    /**
     * Aplicar filtros comunes a query de Activityes
     */
    protected function applyActivityFilters($query, $filters)
    {
        // Filtro por fecha
        if (isset($filters['date'])) {
            $query->availableOnDate($filters['date']);
        }

        // Filtro por rango de precios
        if (isset($filters['min_price'])) {
            $query->minPrice($filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->maxPrice($filters['max_price']);
        }

        // Filtro por popularidad mínima
        if (isset($filters['popularity'])) {
            $query->where('popularity', '>=', $filters['popularity']);
        }

        // Filtro por búsqueda de texto
        if (isset($filters['search'])) {
            $query->searchByTitle($filters['search']);
        }

        return $query;
    }

    /**
     * Aplicar filtros comunes a query de Bookings
     */
    protected function applyBookingFilters($query, $filters)
    {
        // Filtro por Activity
        if (isset($filters['activity_id'])) {
            $query->where('activity_id', $filters['activity_id']);
        }

        // Filtro por rango de fechas de Activity
        if (isset($filters['activity_date_from'])) {
            $query->where('activity_date', '>=', $filters['activity_date_from']);
        }

        if (isset($filters['activity_date_to'])) {
            $query->where('activity_date', '<=', $filters['activity_date_to']);
        }

        // Filtro por rango de fechas de Booking
        if (isset($filters['booking_date_from'])) {
            $query->where('booking_date', '>=', $filters['booking_date_from']);
        }

        if (isset($filters['booking_date_to'])) {
            $query->where('booking_date', '<=', $filters['booking_date_to']);
        }

        // Filtro por estado temporal
        if (isset($filters['status'])) {
            switch ($filters['status']) {
                case 'future':
                    $query->future();
                    break;
                case 'past':
                    $query->past();
                    break;
                case 'today':
                    $query->today();
                    break;
            }
        }

        // Filtro por número mínimo de personas
        if (isset($filters['min_people'])) {
            $query->minPeople($filters['min_people']);
        }

        // Filtro por número máximo de personas
        if (isset($filters['max_people'])) {
            $query->maxPeople($filters['max_people']);
        }

        return $query;
    }

    /**
     * Manejar excepciones comunes
     */
    protected function handleException(\Exception $e, $defaultMessage = 'Ha ocurrido un error inesperado')
    {
        // Log del error para debugging
        \Log::error('Controller Exception: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);

        // En producción, no mostrar detalles del error
        if (app()->environment('production')) {
            return $this->errorResponse($defaultMessage, null, 500);
        }

        // En desarrollo, mostrar detalles
        return $this->errorResponse(
            $defaultMessage,
            [
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ],
            500
        );
    }

    /**
     * Verificar si la request es AJAX
     */
    protected function isAjaxRequest(Request $request)
    {
        return $request->ajax() || $request->wantsJson();
    }

    /**
     * Respuesta unificada para request AJAX o normal
     */
    protected function unifiedResponse(Request $request, $view, $data, $successMessage = null, $errorMessage = null)
    {
        if ($this->isAjaxRequest($request)) {
            if ($errorMessage) {
                return $this->errorResponse($errorMessage);
            }
            return $this->successResponse($data, $successMessage);
        }

        if ($errorMessage) {
            return redirect()->back()->withErrors(['error' => $errorMessage]);
        }

        if ($successMessage) {
            return view($view, $data)->with('success', $successMessage);
        }

        return view($view, $data);
    }

    /**
     * Generar breadcrumbs básicos
     */
    protected function generateBreadcrumbs($items)
    {
        $breadcrumbs = [
            ['title' => 'Inicio', 'url' => route('home')]
        ];

        foreach ($items as $item) {
            $breadcrumbs[] = $item;
        }

        return $breadcrumbs;
    }

    /**
     * Obtener configuración de ordenamiento
     */
    protected function getSortingParams(Request $request, $defaultSort = 'id', $defaultDirection = 'desc')
    {
        $allowedSorts = ['id', 'title', 'price_per_person', 'popularity', 'created_at', 'updated_at'];
        $allowedDirections = ['asc', 'desc'];

        $sort = $request->input('sort', $defaultSort);
        $direction = $request->input('direction', $defaultDirection);

        // Validar parámetros
        if (!in_array($sort, $allowedSorts)) {
            $sort = $defaultSort;
        }

        if (!in_array($direction, $allowedDirections)) {
            $direction = $defaultDirection;
        }

        return [
            'sort' => $sort,
            'direction' => $direction
        ];
    }

    /**
     * Preparar datos para export/API
     */
    protected function prepareDataForExport($items, $type = 'activities')
    {
        if ($type === 'activities') {
            return $items->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'titulo' => $activity->title,
                    'descripcion' => $activity->description,
                    'fecha_inicio' => $activity->start_date->format('Y-m-d'),
                    'fecha_fin' => $activity->end_date->format('Y-m-d'),
                    'precio_por_persona' => $activity->price_per_person,
                    'popularidad' => $activity->popularity,
                    'total_Bookings' => $activity->total_bookings ?? 0,
                    'ingresos_totales' => $activity->total_revenue ?? 0
                ];
            });
        }

        if ($type === 'bookings') {
            return $items->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'Activity' => $booking->activity->title,
                    'numero_personas' => $booking->number_of_people,
                    'precio_Booking' => $booking->booking_price,
                    'fecha_Booking' => $booking->booking_date->format('Y-m-d H:i:s'),
                    'fecha_Activity' => $booking->activity_date->format('Y-m-d'),
                    'estado' => $booking->status
                ];
            });
        }

        return $items;
    }
}
