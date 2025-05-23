<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Crear Activityes específicas con datos realistas
        $this->createSpecificActivities();
        
        // Crear Activityes adicionales con factory
        Activity::factory()
            ->count(15)
            ->create();
        
        // Crear algunas Activityes muy populares
        Activity::factory()
            ->count(5)
            ->popular()
            ->create();
        
        // Crear Activityes disponibles hoy
        Activity::factory()
            ->count(8)
            ->availableToday()
            ->create();
        
        // Crear Activityes baratas
        Activity::factory()
            ->count(6)
            ->cheap()
            ->create();
        
        // Crear Activityes caras
        Activity::factory()
            ->count(4)
            ->expensive()
            ->create();
    }

    private function createSpecificActivities()
    {
        $specificActivities = [
            [
                'title' => 'Tour Completo por Madrid',
                'description' => 'Descubre los lugares más emblemáticos de Madrid en un tour completo que incluye el Palacio Real, la Plaza Mayor, el Parque del Retiro y el Museo del Prado. Una experiencia perfecta para conocer la capital española.',
                'start_date' => Carbon::now()->format('Y-m-d'),
                'end_date' => Carbon::now()->addMonths(6)->format('Y-m-d'),
                'price_per_person' => 45.00,
                'popularity' => 95
            ],
            [
                'title' => 'Visita a la Sagrada Familia y Park Güell',
                'description' => 'Explora las obras maestras de Gaudí en Barcelona. Incluye entrada sin cola a la Sagrada Familia y un paseo guiado por el colorido Park Güell con vistas panorámicas de la ciudad.',
                'start_date' => Carbon::now()->subDays(10)->format('Y-m-d'),
                'end_date' => Carbon::now()->addMonths(4)->format('Y-m-d'),
                'price_per_person' => 65.00,
                'popularity' => 92
            ],
            [
                'title' => 'Ruta de Tapas Auténticas por Sevilla',
                'description' => 'Sumérgete en la cultura gastronómica sevillana visitando los mejores bares de tapas del centro histórico. Incluye degustación de jamón ibérico, flamenquín y vino de Jerez.',
                'start_date' => Carbon::now()->format('Y-m-d'),
                'end_date' => Carbon::now()->addMonths(3)->format('Y-m-d'),
                'price_per_person' => 35.00,
                'popularity' => 88
            ],
            [
                'title' => 'Excursión a Toledo Medieval',
                'description' => 'Viaja en el tiempo visitando la ciudad de las tres culturas. Recorre sus calles empedradas, visita la Catedral de Toledo y conoce la historia de cristianos, judíos y musulmanes.',
                'start_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
                'end_date' => Carbon::now()->addMonths(5)->format('Y-m-d'),
                'price_per_person' => 55.00,
                'popularity' => 85
            ],
            [
                'title' => 'Cata de Vinos en La Rioja',
                'description' => 'Descubre los mejores caldos de La Rioja visitando bodegas tradicionales y modernas. Incluye cata dirigida, maridaje con productos locales y visita a viñedos.',
                'start_date' => Carbon::now()->addWeeks(2)->format('Y-m-d'),
                'end_date' => Carbon::now()->addMonths(8)->format('Y-m-d'),
                'price_per_person' => 75.00,
                'popularity' => 78
            ],
            [
                'title' => 'Senderismo en los Picos de Europa',
                'description' => 'Aventura en plena naturaleza recorriendo los senderos más espectaculares de los Picos de Europa. Activity para amantes del trekking con guía especializado.',
                'start_date' => Carbon::now()->addMonths(1)->format('Y-m-d'),
                'end_date' => Carbon::now()->addMonths(7)->format('Y-m-d'),
                'price_per_person' => 85.00,
                'popularity' => 72
            ],
            [
                'title' => 'Tour Nocturno por Granada',
                'description' => 'Experimenta la magia de Granada al anochecer con vistas privilegiadas de la Alhambra iluminada desde el Mirador de San Nicolás. Incluye espectáculo de flamenco.',
                'start_date' => Carbon::now()->format('Y-m-d'),
                'end_date' => Carbon::now()->addMonths(4)->format('Y-m-d'),
                'price_per_person' => 42.00,
                'popularity' => 90
            ],
            [
                'title' => 'Clase de Cocina Española',
                'description' => 'Aprende a cocinar auténtica paella valenciana y otros platos tradicionales españoles de la mano de chefs locales. Incluye degustación y recetas para casa.',
                'start_date' => Carbon::now()->addDays(3)->format('Y-m-d'),
                'end_date' => Carbon::now()->addMonths(2)->format('Y-m-d'),
                'price_per_person' => 58.00,
                'popularity' => 76
            ]
        ];

        foreach ($specificActivities as $activity) {
            Activity::create($activity);
        }
    }
}
