<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Mostrar listado de Bookings (opcional - para administración)
     */
    public function index(Request $request)
    {
        $query = Booking::with('activity')->orderByDesc('booking_date');

        // Aplicar filtros usando el método del controlador base
        $filters = $request->only(['activity_id', 'activity_date_from', 'activity_date_to', 'status', 'min_people', 'max_people']);
        $query = $this->applyBookingFilters($query, $filters);

        $paginationParams = $this->getPaginationParams($request);
        $bookings = $query->paginate($paginationParams['per_page']);
        
        $activities = Activity::orderBy('title')->get(['id', 'title']);

        return view('bookings.index', compact('bookings', 'activities'));
    }

    /**
     * Mostrar detalle de una Booking específica
     */
    public function show(Booking $booking)
    {
        $booking->load('activity');
        
        $breadcrumbs = $this->generateBreadcrumbs([
            ['title' => 'Bookings', 'url' => route('bookings.index')],
            ['title' => 'Booking #' . $booking->id, 'url' => null]
        ]);

        return view('bookings.show', compact('booking', 'breadcrumbs'));
    }

    /**
     * Procesar una nueva Booking - MÉTODO PRINCIPAL PARA LA PRUEBA
     */
    public function store(Request $request)
{
    try {
        \Log::info('Store method called', $request->all());
        
        // Validación básica
        $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'activity_date' => 'required|date',
            'number_of_people' => 'required|integer|min:1'
        ]);

        // Usar el método createBooking del modelo
        $booking = Booking::createBooking(
            $request->activity_id,
            $request->number_of_people,
            $request->activity_date
        );

        

         \Log::info('Booking created with ID: ' . $booking->id);

        // Redirigir a la confirmación
        return redirect()->route('bookings.confirmation', $booking->id)
            ->with('success', 'Reserva #' . $booking->id . ' creada exitosamente');

    } catch (\Exception $e) {
        \Log::error('Booking error: ' . $e->getMessage());
        
        return redirect()->back()
            ->with('error', 'Error: ' . $e->getMessage())
            ->withInput();
    }
}

    /**
     * Mostrar confirmación de Booking - REQUERIDO PARA LA PRUEBA
     */
    public function confirmation(Booking $booking)
    {
        $booking->load('activity');
        
        $breadcrumbs = $this->generateBreadcrumbs([
            ['title' => 'Confirmación de Booking', 'url' => null]
        ]);

        return view('bookings.confirmation', compact('booking', 'breadcrumbs'));
    }

    /**
     * Cancelar una Booking
     */
    public function cancel(Booking $booking, Request $request)
    {
        // Verificar si se puede cancelar
        if (!$booking->canBeCancelled()) {
            $errorMessage = 'No se puede cancelar la Booking. El plazo de cancelación ha expirado.';
            
            if ($this->isAjaxRequest($request)) {
                return $this->errorResponse($errorMessage, null, 422);
            }
            
            return redirect()->back()->withErrors(['error' => $errorMessage]);
        }

        try {
            DB::beginTransaction();

            // Aquí podrías agregar lógica adicional como:
            // - Enviar email de cancelación
            // - Calcular reembolso
            // - Actualizar estadísticas

            $activityTitle = $booking->activity->title;
            $booking->delete();

            DB::commit();

            $successMessage = "Booking para '{$activityTitle}' cancelada exitosamente.";
            
            if ($this->isAjaxRequest($request)) {
                return $this->successResponse(null, $successMessage);
            }

            return redirect()->route('home')->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, 'Error al cancelar la Booking.');
        }
    }

    /**
     * Modificar una Booking
     */
    public function update(Booking $booking, Request $request)
    {
        // Verificar si se puede modificar
        if (!$booking->canBeModified()) {
            $errorMessage = 'No se puede modificar la Booking. El plazo de modificación ha expirado.';
            
            if ($this->isAjaxRequest($request)) {
                return $this->errorResponse($errorMessage, null, 422);
            }
            
            return redirect()->back()->withErrors(['error' => $errorMessage]);
        }

        $validated = $request->validate([
            'number_of_people' => 'required|integer|min:1|max:20',
            'activity_date' => 'required|date|after_or_equal:today'
        ]);

        try {
            DB::beginTransaction();

            $activity = $booking->activity;

            // Verificar nueva disponibilidad
            if (!$activity->isAvailableOnDate($validated['activity_date'])) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['activity_date' => 'La Activity no está disponible en la nueva fecha seleccionada.']);
            }

            // Recalcular precio
            $newPrice = $activity->calculateTotalPrice($validated['number_of_people']);

            // Actualizar Booking
            $booking->update([
                'number_of_people' => $validated['number_of_people'],
                'activity_date' => $validated['activity_date'],
                'booking_price' => $newPrice
            ]);

            DB::commit();

            $successMessage = 'Booking modificada exitosamente.';
            
            if ($this->isAjaxRequest($request)) {
                return $this->successResponse([
                    'booking' => [
                        'id' => $booking->id,
                        'number_of_people' => $booking->number_of_people,
                        'activity_date' => $booking->formatted_activity_date,
                        'total_price' => $booking->formatted_price
                    ]
                ], $successMessage);
            }

            return redirect()->route('bookings.show', $booking)->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleException($e, 'Error al modificar la Booking.');
        }
    }

    /**
     * Verificar disponibilidad antes de mostrar formulario
     */
    public function checkAvailability(Request $request)
    {
        try {
            $validated = $request->validate([
                'activity_id' => 'required|exists:activities,id',
                'activity_date' => 'required|date|after_or_equal:today',
                'number_of_people' => 'required|integer|min:1|max:20'
            ]);

            $activity = Activity::findOrFail($validated['activity_id']);
            $isAvailable = $activity->isAvailableOnDate($validated['activity_date']);
            $hasCapacity = $activity->hasAvailabilityForDate($validated['activity_date'], $validated['number_of_people']);

            return $this->successResponse([
                'available' => $isAvailable && $hasCapacity,
                'total_price' => $activity->calculateTotalPrice($validated['number_of_people']),
                'price_per_person' => $activity->price_per_person,
                'formatted_total_price' => $this->formatPrice($activity->calculateTotalPrice($validated['number_of_people'])),
                'formatted_price_per_person' => $activity->formatted_price,
                'activity_title' => $activity->title,
                'formatted_date' => $this->formatDate($validated['activity_date']),
                'people_already_booked' => $activity->getPeopleBookedForDate($validated['activity_date']),
                'activity_details' => [
                    'description' => $activity->short_description,
                    'popularity' => $activity->popularity,
                    'available_from' => $this->formatDate($activity->start_date),
                    'available_until' => $this->formatDate($activity->end_date)
                ]
            ], 'Disponibilidad verificada correctamente.');

        } catch (\Exception $e) {
            return $this->handleException($e, 'Error al verificar disponibilidad.');
        }
    }

    /**
     * Obtener estadísticas de Bookings
     */
    public function stats(Request $request)
    {
        try {
            $startDate = $request->input('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
            $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

            $stats = Booking::getBookingStats($startDate, $endDate);
            $mostBookedActivities = Booking::getMostBookedActivities(10);
            
            // Estadísticas adicionales por período
            $bookingsByMonth = Booking::selectRaw('YEAR(booking_date) as year, MONTH(booking_date) as month, COUNT(*) as count, SUM(booking_price) as revenue')
                ->whereBetween('booking_date', [$startDate, $endDate])
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();

            $bookingsByDay = Booking::selectRaw('DATE(booking_date) as date, COUNT(*) as count, SUM(booking_price) as revenue')
                ->whereBetween('booking_date', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->limit(30)
                ->get();

            // Estadísticas por tamaño de grupo
            $bookingsByGroupSize = Booking::selectRaw('number_of_people, COUNT(*) as count')
                ->whereBetween('booking_date', [$startDate, $endDate])
                ->groupBy('number_of_people')
                ->orderBy('number_of_people')
                ->get();

            return $this->successResponse([
                'general_stats' => $stats,
                'most_booked_activities' => $mostBookedActivities,
                'bookings_by_month' => $bookingsByMonth,
                'bookings_by_day' => $bookingsByDay,
                'bookings_by_group_size' => $bookingsByGroupSize,
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'formatted_start_date' => $this->formatDate($startDate),
                    'formatted_end_date' => $this->formatDate($endDate)
                ]
            ], 'Estadísticas obtenidas correctamente.');

        } catch (\Exception $e) {
            return $this->handleException($e, 'Error al obtener estadísticas.');
        }
    }

    /**
     * Buscar Bookings por criterios
     */
    public function search(Request $request)
    {
        try {
            $query = Booking::with('activity');

            // Aplicar filtros específicos de búsqueda
            if ($request->filled('booking_id')) {
                $query->where('id', $request->booking_id);
            }

            if ($request->filled('activity_title')) {
                $query->whereHas('activity', function ($q) use ($request) {
                    $q->where('title', 'like', '%' . $request->activity_title . '%');
                });
            }

            // Aplicar filtros comunes
            $filters = $request->only(['activity_date_from', 'activity_date_to', 'booking_date_from', 'booking_date_to', 'status', 'min_people', 'max_people']);
            $query = $this->applyBookingFilters($query, $filters);

            // Aplicar ordenamiento
            $sortParams = $this->getSortingParams($request, 'booking_date', 'desc');
            $query->orderBy($sortParams['sort'], $sortParams['direction']);

            $paginationParams = $this->getPaginationParams($request);
            $bookings = $query->paginate($paginationParams['per_page']);

            if ($this->isAjaxRequest($request)) {
                return $this->successResponse([
                    'bookings' => $this->prepareDataForExport($bookings, 'bookings'),
                    'pagination' => [
                        'current_page' => $bookings->currentPage(),
                        'last_page' => $bookings->lastPage(),
                        'per_page' => $bookings->perPage(),
                        'total' => $bookings->total()
                    ]
                ], 'Búsqueda completada correctamente.');
            }

            return view('bookings.search', compact('bookings'));

        } catch (\Exception $e) {
            return $this->handleException($e, 'Error en la búsqueda de Bookings.');
        }
    }

    /**
     * Exportar Bookings (opcional)
     */
    public function export(Request $request)
    {
        try {
            $query = Booking::with('activity');
            
            // Aplicar los mismos filtros que en search
            $filters = $request->only(['activity_id', 'activity_date_from', 'activity_date_to', 'status']);
            $query = $this->applyBookingFilters($query, $filters);

            $bookings = $query->orderByDesc('booking_date')->get();
            $exportData = $this->prepareDataForExport($bookings, 'bookings');

            return $this->successResponse($exportData, 'Datos exportados correctamente.');

        } catch (\Exception $e) {
            return $this->handleException($e, 'Error al exportar datos.');
        }
    }

    /**
     * Obtener resumen de Booking para preview
     */
    public function preview(Request $request)
    {
        try {
            $validated = $this->validateBookingParams($request);
            
            $activity = Activity::findOrFail($validated['activity_id']);
            
            if (!$activity->isAvailableOnDate($validated['activity_date'])) {
                return $this->errorResponse('La Activity no está disponible en la fecha seleccionada.', null, 422);
            }

            $totalPrice = $activity->calculateTotalPrice($validated['number_of_people']);

            return $this->successResponse([
                'activity' => [
                    'id' => $activity->id,
                    'title' => $activity->title,
                    'description' => $activity->short_description,
                    'price_per_person' => $activity->price_per_person,
                    'formatted_price_per_person' => $activity->formatted_price
                ],
                'booking_details' => [
                    'activity_date' => $validated['activity_date'],
                    'formatted_activity_date' => $this->formatDate($validated['activity_date']),
                    'number_of_people' => $validated['number_of_people'],
                    'total_price' => $totalPrice,
                    'formatted_total_price' => $this->formatPrice($totalPrice)
                ],
                'availability' => [
                    'is_available' => true,
                    'people_already_booked' => $activity->getPeopleBookedForDate($validated['activity_date'])
                ]
            ], 'Preview de Booking generado correctamente.');

        } catch (\Exception $e) {
            return $this->handleException($e, 'Error al generar preview de Booking.');
        }
    }
}
