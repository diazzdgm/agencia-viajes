<?php

namespace Database\Factories;

use App\Models\RelatedActivity;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RelatedActivity>
 */
class RelatedActivityFactory extends Factory
{
    protected $model = RelatedActivity::class;

    public function definition()
    {
        // Obtener dos Activityes diferentes
        $activities = Activity::inRandomOrder()->limit(2)->get();
        
        // Si no hay suficientes Activityes, crear las necesarias
        if ($activities->count() < 2) {
            $activity1 = Activity::factory()->create();
            $activity2 = Activity::factory()->create();
        } else {
            $activity1 = $activities->first();
            $activity2 = $activities->last();
        }

        return [
            'activity_id' => $activity1->id,
            'related_activity_id' => $activity2->id,
        ];
    }

    public function forActivity(Activity $activity)
    {
        return $this->state(function (array $attributes) use ($activity) {
            // Buscar una Activity relacionada que no sea la misma
            $relatedActivity = Activity::where('id', '!=', $activity->id)
                ->inRandomOrder()
                ->first();
            
            // Si no hay otra Activity, crear una
            if (!$relatedActivity) {
                $relatedActivity = Activity::factory()->create();
            }

            return [
                'activity_id' => $activity->id,
                'related_activity_id' => $relatedActivity->id,
            ];
        });
    }
}
