<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelatedActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'related_activity_id',
    ];

    public $timestamps = false; // No necesitamos timestamps para esta tabla de relación

    /**
     * Relación con la Activity principal
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    /**
     * Relación con la Activity relacionada
     */
    public function relatedActivity()
    {
        return $this->belongsTo(Activity::class, 'related_activity_id');
    }

    /**
     * Scope para obtener Activityes relacionadas disponibles en una fecha específica
     */
    public function scopeAvailableOnDate($query, $date)
    {
        return $query->whereHas('relatedActivity', function ($q) use ($date) {
            $q->where('start_date', '<=', $date)
              ->where('end_date', '>=', $date);
        });
    }

    /**
     * Scope para evitar relaciones duplicadas
     */
    public function scopeNotDuplicate($query, $activityId, $relatedActivityId)
    {
        return $query->where(function ($q) use ($activityId, $relatedActivityId) {
            $q->where('activity_id', $activityId)
              ->where('related_activity_id', $relatedActivityId);
        })->orWhere(function ($q) use ($activityId, $relatedActivityId) {
            $q->where('activity_id', $relatedActivityId)
              ->where('related_activity_id', $activityId);
        });
    }

    /**
     * Boot method to add model events
     */
    protected static function boot()
    {
        parent::boot();

        // Validar antes de crear que no sea la misma Activity
        static::creating(function ($model) {
            if ($model->activity_id == $model->related_activity_id) {
                throw new \InvalidArgumentException('Una Activity no puede estar relacionada consigo misma.');
            }
        });

        // Validar antes de actualizar que no sea la misma Activity
        static::updating(function ($model) {
            if ($model->activity_id == $model->related_activity_id) {
                throw new \InvalidArgumentException('Una Activity no puede estar relacionada consigo misma.');
            }
        });
    }
}
