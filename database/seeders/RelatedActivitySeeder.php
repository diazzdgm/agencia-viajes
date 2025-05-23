<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\RelatedActivity;
use Illuminate\Database\Seeder;

class RelatedActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createSpecificRelatedActivities();
        $this->createRandomRelatedActivities();
    }

    private function createSpecificRelatedActivities()
    {
        // Obtener Activityes específicas por título para crear relaciones lógicas
        $madridTour = Activity::where('title', 'Tour Completo por Madrid')->first();
        $toledoTour = Activity::where('title', 'Excursión a Toledo Medieval')->first();
        $sagradaFamilia = Activity::where('title', 'Visita a la Sagrada Familia y Park Güell')->first();
        $sevillaTapas = Activity::where('title', 'Ruta de Tapas Auténticas por Sevilla')->first();
        $granadaTour = Activity::where('title', 'Tour Nocturno por Granada')->first();
        $riojaCata = Activity::where('title', 'Cata de Vinos en La Rioja')->first();
        $cookingClass = Activity::where('title', 'Clase de Cocina Española')->first();

        // Crear relaciones lógicas entre Activityes
        $relations = [];

        // Madrid relacionado con Toledo (cercanía geográfica)
        if ($madridTour && $toledoTour) {
            $relations[] = ['activity_id' => $madridTour->id, 'related_activity_id' => $toledoTour->id];
            $relations[] = ['activity_id' => $toledoTour->id, 'related_activity_id' => $madridTour->id];
        }

        // Activityes gastronómicas relacionadas
        if ($sevillaTapas && $cookingClass) {
            $relations[] = ['activity_id' => $sevillaTapas->id, 'related_activity_id' => $cookingClass->id];
            $relations[] = ['activity_id' => $cookingClass->id, 'related_activity_id' => $sevillaTapas->id];
        }

        if ($riojaCata && $cookingClass) {
            $relations[] = ['activity_id' => $riojaCata->id, 'related_activity_id' => $cookingClass->id];
        }

        if ($riojaCata && $sevillaTapas) {
            $relations[] = ['activity_id' => $riojaCata->id, 'related_activity_id' => $sevillaTapas->id];
        }

        // Tours culturales relacionados
        if ($granadaTour && $sevillaTapas) {
            $relations[] = ['activity_id' => $granadaTour->id, 'related_activity_id' => $sevillaTapas->id];
        }

        if ($madridTour && $sagradaFamilia) {
            $relations[] = ['activity_id' => $madridTour->id, 'related_activity_id' => $sagradaFamilia->id];
            $relations[] = ['activity_id' => $sagradaFamilia->id, 'related_activity_id' => $madridTour->id];
        }

        // Insertar las relaciones evitando duplicados
        foreach ($relations as $relation) {
            $exists = RelatedActivity::where('activity_id', $relation['activity_id'])
                ->where('related_activity_id', $relation['related_activity_id'])
                ->exists();
            
            if (!$exists) {
                RelatedActivity::create($relation);
            }
        }
    }

    private function createRandomRelatedActivities()
    {
        $activities = Activity::all();
        
        // Crear relaciones adicionales aleatorias para cada Activity
        foreach ($activities as $activity) {
            // Número aleatorio de Activityes relacionadas (0-3)
            $numberOfRelations = rand(0, 3);
            
            for ($i = 0; $i < $numberOfRelations; $i++) {
                $relatedActivity = $activities->where('id', '!=', $activity->id)->random();
                
                // Verificar que la relación no existe ya
                $exists = RelatedActivity::where('activity_id', $activity->id)
                    ->where('related_activity_id', $relatedActivity->id)
                    ->exists();
                
                if (!$exists) {
                    RelatedActivity::create([
                        'activity_id' => $activity->id,
                        'related_activity_id' => $relatedActivity->id,
                    ]);
                }
            }
        }
    }
}
