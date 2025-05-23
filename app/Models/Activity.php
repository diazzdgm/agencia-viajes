<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'price_per_person',
        'popularity'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'price_per_person' => 'decimal:2',
        'popularity' => 'integer'
    ];

    /**
     * Relación con las Bookings
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Activityes relacionadas (como Activity principal)
     */
    public function relatedActivities()
    {
        return $this->belongsToMany(
            Activity::class,
            'related_activities',
            'activity_id',
            'related_activity_id'
        );
    }

    /**
     * Activityes que tienen esta como relacionada
     */
    public function relatedFrom()
    {
        return $this->belongsToMany(
            Activity::class,
            'related_activities',
            'related_activity_id',
            'activity_id'
        );
    }

    /**
     * Relaciones directas con la tabla pivot
     */
    public function relatedActivityPivots()
    {
        return $this->hasMany(RelatedActivity::class, 'activity_id');
    }

    public function relatedFromPivots()
    {
        return $this->hasMany(RelatedActivity::class, 'related_activity_id');
    }

    /**
     * Scope para Activityes disponibles en una fecha específica
     */
    public function scopeAvailableOnDate($query, $date)
    {
        return $query->where('start_date', '<=', $date)
                    ->where('end_date', '>=', $date);
    }

    /**
     * Scope para ordenar por popularidad
     */
    public function scopeOrderByPopularity($query, $direction = 'desc')
    {
        return $query->orderBy('popularity', $direction);
    }

    /**
     * Scope para Activityes con precio máximo
     */
    public function scopeMaxPrice($query, $maxPrice)
    {
        return $query->where('price_per_person', '<=', $maxPrice);
    }

    /**
     * Scope para Activityes con precio mínimo
     */
    public function scopeMinPrice($query, $minPrice)
    {
        return $query->where('price_per_person', '>=', $minPrice);
    }

    /**
     * Scope para buscar por título
     */
    public function scopeSearchByTitle($query, $search)
    {
        return $query->where('title', 'like', '%' . $search . '%');
    }

    /**
     * Scope para obtener Activityes con sus relacionadas
     */
    public function scopeWithRelated($query)
    {
        return $query->with(['relatedActivities', 'relatedFrom']);
    }

    /**
     * Obtener todas las Activityes relacionadas (bidireccional)
     */
    public function getAllRelatedActivities()
    {
        return $this->relatedActivities->merge($this->relatedFrom)->unique('id');
    }

    /**
     * Obtener Activityes relacionadas disponibles en una fecha específica
     */
    public function getRelatedActivitiesForDate($date)
    {
        $relatedIds = RelatedActivity::where('activity_id', $this->id)
            ->pluck('related_activity_id')
            ->merge(
                RelatedActivity::where('related_activity_id', $this->id)
                    ->pluck('activity_id')
            );

        return Activity::whereIn('id', $relatedIds)
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->orderBy('popularity', 'desc')
            ->get();
    }

    /**
     * Calcular precio total para un número de personas
     */
    public function calculateTotalPrice($numberOfPeople)
    {
        return $this->price_per_person * $numberOfPeople;
    }

    /**
     * Verificar si la Activity está disponible en una fecha
     */
    public function isAvailableOnDate($date)
    {
        $checkDate = Carbon::parse($date);
        return $checkDate->between(
            Carbon::parse($this->start_date),
            Carbon::parse($this->end_date)
        );
    }

    /**
     * Obtener el número total de Bookings para esta Activity
     */
    public function getTotalBookingsAttribute()
    {
        return $this->bookings()->count();
    }

    /**
     * Obtener el número total de personas que han Bookingdo esta Activity
     */
    public function getTotalPeopleBookedAttribute()
    {
        return $this->bookings()->sum('number_of_people');
    }

    /**
     * Obtener los ingresos totales de esta Activity
     */
    public function getTotalRevenueAttribute()
    {
        return $this->bookings()->sum('booking_price');
    }

    /**
     * Obtener Bookings para una fecha específica
     */
    public function getBookingsForDate($date)
    {
        return $this->bookings()->where('activity_date', $date)->get();
    }

    /**
     * Obtener el número de personas Bookingdas para una fecha específica
     */
    public function getPeopleBookedForDate($date)
    {
        return $this->bookings()
            ->where('activity_date', $date)
            ->sum('number_of_people');
    }

    /**
     * Verificar si hay cupo disponible (opcional, si tienes límite de personas)
     */
    public function hasAvailabilityForDate($date, $requestedPeople = 1)
    {
        // Si no tienes límite máximo de personas, siempre hay disponibilidad
        // Puedes agregar un campo 'max_capacity' si lo necesitas
        return $this->isAvailableOnDate($date);
    }

    /**
     * Formatear el precio para mostrar
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price_per_person, 2, ',', '.') . ' €';
    }

    /**
     * Obtener el título truncado para listados
     */
    public function getShortTitleAttribute()
    {
        return \Str::limit($this->title, 50);
    }

    /**
     * Obtener la descripción truncada
     */
    public function getShortDescriptionAttribute()
    {
        return \Str::limit($this->description, 150);
    }

    /**
     * Scope para Activityes populares (popularity > threshold)
     */
    public function scopePopular($query, $threshold = 70)
    {
        return $query->where('popularity', '>', $threshold);
    }

    /**
     * Scope para Activityes disponibles actualmente
     */
    public function scopeCurrentlyAvailable($query)
    {
        $today = Carbon::today();
        return $query->where('start_date', '<=', $today)
                    ->where('end_date', '>=', $today);
    }

    /**
     * Obtener Activityes similares basadas en precio
     */
    public function getSimilarActivitiesByPrice($priceRange = 20)
    {
        $minPrice = $this->price_per_person - $priceRange;
        $maxPrice = $this->price_per_person + $priceRange;

        return Activity::where('id', '!=', $this->id)
            ->whereBetween('price_per_person', [$minPrice, $maxPrice])
            ->orderBy('popularity', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Método para agregar una Activity relacionada
     */
    public function addRelatedActivity($relatedActivityId)
    {
        // Verificar que no sea la misma Activity
        if ($this->id == $relatedActivityId) {
            return false;
        }

        // Verificar que no exista ya la relación
        $exists = RelatedActivity::where('activity_id', $this->id)
            ->where('related_activity_id', $relatedActivityId)
            ->exists();

        if (!$exists) {
            RelatedActivity::create([
                'activity_id' => $this->id,
                'related_activity_id' => $relatedActivityId
            ]);
            return true;
        }

        return false;
    }

    /**
     * Método para eliminar una Activity relacionada
     */
    public function removeRelatedActivity($relatedActivityId)
    {
        return RelatedActivity::where('activity_id', $this->id)
            ->where('related_activity_id', $relatedActivityId)
            ->delete();
    }
}
