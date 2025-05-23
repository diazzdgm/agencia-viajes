<?php

namespace App\Models;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'number_of_people',
        'booking_price',
        'total_price',
        'booking_date',
        'activity_date'
    ];

    protected $casts = [
        'booking_date' => 'datetime',
        'activity_date' => 'date',
        'booking_price' => 'decimal:2',
        'number_of_people' => 'integer'
    ];

    /**
     * Relación con la Activity
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Scope para Bookings de una fecha específica
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('activity_date', $date);
    }

    /**
     * Scope para Bookings realizadas en un periodo
     */
    public function scopeBookedBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('booking_date', [$startDate, $endDate]);
    }

    /**
     * Scope para Activityes realizadas en un periodo
     */
    public function scopeActivitiesBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('activity_date', [$startDate, $endDate]);
    }

    /**
     * Scope para Bookings de una Activity específica
     */
    public function scopeForActivity($query, $activityId)
    {
        return $query->where('activity_id', $activityId);
    }

    /**
     * Scope para Bookings con un mínimo de personas
     */
    public function scopeMinPeople($query, $minPeople)
    {
        return $query->where('number_of_people', '>=', $minPeople);
    }

    /**
     * Scope para Bookings con un máximo de personas
     */
    public function scopeMaxPeople($query, $maxPeople)
    {
        return $query->where('number_of_people', '<=', $maxPeople);
    }

    /**
     * Scope para Bookings recientes
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('booking_date', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Scope para Bookings futuras
     */
    public function scopeFuture($query)
    {
        return $query->where('activity_date', '>', Carbon::today());
    }

    /**
     * Scope para Bookings pasadas
     */
    public function scopePast($query)
    {
        return $query->where('activity_date', '<', Carbon::today());
    }

    /**
     * Scope para Bookings de hoy
     */
    public function scopeToday($query)
    {
        return $query->where('activity_date', Carbon::today());
    }

    /**
     * Scope para Bookings ordenadas por fecha de Booking
     */
    public function scopeOrderByBookingDate($query, $direction = 'desc')
    {
        return $query->orderBy('booking_date', $direction);
    }

    /**
     * Scope para Bookings ordenadas por fecha de Activity
     */
    public function scopeOrderByActivityDate($query, $direction = 'asc')
    {
        return $query->orderBy('activity_date', $direction);
    }

    /**
     * Scope para Bookings ordenadas por precio
     */
    public function scopeOrderByPrice($query, $direction = 'desc')
    {
        return $query->orderBy('booking_price', $direction);
    }

    /**
     * Obtener el precio por persona de esta Booking
     */
    public function getPricePerPersonAttribute()
    {
        return $this->booking_price / $this->number_of_people;
    }

    /**
     * Verificar si la Booking es para hoy
     */
    public function getIsTodayAttribute()
    {
        return Carbon::parse($this->activity_date)->isToday();
    }

    /**
     * Verificar si la Booking es futura
     */
    public function getIsFutureAttribute()
    {
        return Carbon::parse($this->activity_date)->isFuture();
    }

    /**
     * Verificar si la Booking es pasada
     */
    public function getIsPastAttribute()
    {
        return Carbon::parse($this->activity_date)->isPast();
    }

    /**
     * Obtener los días hasta la Activity
     */
    public function getDaysUntilActivityAttribute()
    {
        return Carbon::today()->diffInDays(Carbon::parse($this->activity_date), false);
    }

    /**
     * Obtener los días desde que se hizo la Booking
     */
    public function getDaysSinceBookingAttribute()
    {
        return Carbon::parse($this->booking_date)->diffInDays(Carbon::now());
    }

    /**
     * Formatear el precio de la Booking
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->booking_price, 2, ',', '.') . ' €';
    }

    /**
     * Formatear el precio por persona
     */
    public function getFormattedPricePerPersonAttribute()
    {
        return number_format($this->price_per_person, 2, ',', '.') . ' €';
    }

    /**
     * Obtener la fecha de Booking formateada
     */
    public function getFormattedBookingDateAttribute()
    {
        return Carbon::parse($this->booking_date)->format('d/m/Y H:i');
    }

    /**
     * Obtener la fecha de Activity formateada
     */
    public function getFormattedActivityDateAttribute()
    {
        return Carbon::parse($this->activity_date)->format('d/m/Y');
    }

    /**
     * Obtener el estado de la Booking
     */
    public function getStatusAttribute()
    {
        $activityDate = Carbon::parse($this->activity_date);
        
        if ($activityDate->isToday()) {
            return 'hoy';
        } elseif ($activityDate->isFuture()) {
            return 'confirmada';
        } else {
            return 'realizada';
        }
    }

    /**
     * Obtener el color del estado para UI
     */
    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case 'hoy':
                return 'warning';
            case 'confirmada':
                return 'success';
            case 'realizada':
                return 'secondary';
            default:
                return 'primary';
        }
    }

    /**
     * Verificar si se puede modificar la Booking
     */
    public function canBeModified($hoursBeforeActivity = 24)
    {
        $activityDateTime = Carbon::parse($this->activity_date);
        $cutoffTime = $activityDateTime->subHours($hoursBeforeActivity);
        
        return Carbon::now()->lt($cutoffTime);
    }

    /**
     * Verificar si se puede cancelar la Booking
     */
    public function canBeCancelled($hoursBeforeActivity = 48)
    {
        $activityDateTime = Carbon::parse($this->activity_date);
        $cutoffTime = $activityDateTime->subHours($hoursBeforeActivity);
        
        return Carbon::now()->lt($cutoffTime);
    }

    /**
     * Calcular el precio total basado en Activity y número de personas
     */
    public static function calculatePrice(Activity $activity, $numberOfPeople)
    {
        return $activity->price_per_person * $numberOfPeople;
    }

    /**
     * Crear una nueva Booking
     */
    public static function createBooking($activityId, $numberOfPeople, $activityDate)
    {
        $activity = Activity::findOrFail($activityId);
        
        // Verificar que la Activity esté disponible en la fecha
        if (!$activity->isAvailableOnDate($activityDate)) {
            throw new \Exception('La Activity no está disponible en la fecha seleccionada.');
        }

        $bookingPrice = self::calculatePrice($activity, $numberOfPeople);


        return self::create([
            'activity_id' => $activityId,
            'number_of_people' => $numberOfPeople,
            'booking_price' => $bookingPrice,
            'total_price' => $bookingPrice, 
            'booking_date' => Carbon::now(),
            'activity_date' => $activityDate,
        ]);
    }

    /**
     * Obtener Bookings por mes
     */
    public static function getBookingsByMonth($year = null, $month = null)
    {
        $query = static::query();
        
        if ($year) {
            $query->whereYear('booking_date', $year);
        }
        
        if ($month) {
            $query->whereMonth('booking_date', $month);
        }
        
        return $query->get();
    }

    /**
     * Obtener estadísticas de Bookings
     */
    public static function getBookingStats($startDate = null, $endDate = null)
    {
        $query = static::query();
        
        if ($startDate && $endDate) {
            $query->whereBetween('booking_date', [$startDate, $endDate]);
        }
        
        return [
            'total_bookings' => $query->count(),
            'total_people' => $query->sum('number_of_people'),
            'total_revenue' => $query->sum('booking_price'),
            'average_people_per_booking' => $query->avg('number_of_people'),
            'average_price_per_booking' => $query->avg('booking_price'),
        ];
    }

    /**
     * Obtener las Activityes más Bookingdas
     */
    public static function getMostBookedActivities($limit = 10)
    {
        return static::select('activity_id')
            ->selectRaw('COUNT(*) as booking_count')
            ->selectRaw('SUM(number_of_people) as total_people')
            ->selectRaw('SUM(booking_price) as total_revenue')
            ->with('activity')
            ->groupBy('activity_id')
            ->orderByDesc('booking_count')
            ->limit($limit)
            ->get();
    }
}
