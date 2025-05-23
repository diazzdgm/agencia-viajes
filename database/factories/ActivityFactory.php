<?php

namespace Database\Factories;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition()
    {
        $startDate = $this->faker->dateTimeBetween('-1 month', '+6 months');
        $endDate = Carbon::parse($startDate)->addDays($this->faker->numberBetween(1, 90));
        
        $activities = [
            'Visita guiada al Museo del Prado',
            'Tour gastronómico por Madrid',
            'Excursión a Toledo desde Madrid',
            'Ruta de tapas por Barcelona',
            'Visita a la Sagrada Familia',
            'Tour por el Parque Güell',
            'Excursión a Montserrat',
            'Paseo en barco por la Costa Brava',
            'Visita a la Alhambra de Granada',
            'Tour por el Barrio de Santa Cruz en Sevilla',
            'Espectáculo de flamenco en Sevilla',
            'Ruta por los pueblos blancos de Andalucía',
            'Visita al Guggenheim de Bilbao',
            'Excursión a San Sebastián',
            'Tour por el Camino de Santiago',
            'Visita a las Cuevas de Altamira',
            'Ruta por la Rioja con cata de vinos',
            'Excursión a Santiago de Compostela',
            'Tour nocturno por Valencia',
            'Visita a la Ciudad de las Artes y las Ciencias',
            'Excursión a las Islas Baleares',
            'Tour por Las Palmas de Gran Canaria',
            'Senderismo en el Teide',
            'Visita a Cuenca y sus casas colgadas',
            'Tour por Salamanca',
            'Excursión a Segovia',
            'Ruta por Ávila',
            'Visita al Monasterio del Escorial',
            'Tour por Aranjuez',
            'Excursión a Chinchón'
        ];

        $descriptions = [
            'Una experiencia única para descubrir la rica historia y cultura española.',
            'Sumérgete en los sabores auténticos de la gastronomía local.',
            'Un recorrido fascinante por lugares emblemáticos y llenos de historia.',
            'Descubre los secretos mejor guardados de esta increíble ciudad.',
            'Una aventura cultural que te transportará a través de los siglos.',
            'Explora paisajes únicos y tradiciones ancestrales.',
            'Una experiencia gastronómica que deleitará todos tus sentidos.',
            'Recorre los rincones más pintorescos y auténticos.',
            'Una jornada llena de arte, historia y belleza arquitectónica.',
            'Vive la esencia de España a través de sus monumentos más icónicos.'
        ];

        $title = $this->faker->randomElement($activities);
        
        return [
            'title' => $title,
            'description' => $this->faker->randomElement($descriptions) . ' ' . $this->faker->paragraph(2),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'price_per_person' => $this->faker->randomFloat(2, 15, 250),
            'popularity' => $this->faker->numberBetween(1, 100)
        ];
    }

    public function popular()
    {
        return $this->state(function (array $attributes) {
            return [
                'popularity' => $this->faker->numberBetween(80, 100),
            ];
        });
    }

    public function lowPopularity()
    {
        return $this->state(function (array $attributes) {
            return [
                'popularity' => $this->faker->numberBetween(1, 30),
            ];
        });
    }

    public function expensive()
    {
        return $this->state(function (array $attributes) {
            return [
                'price_per_person' => $this->faker->randomFloat(2, 100, 500),
            ];
        });
    }

    public function cheap()
    {
        return $this->state(function (array $attributes) {
            return [
                'price_per_person' => $this->faker->randomFloat(2, 10, 50),
            ];
        });
    }

    public function availableToday()
    {
        $today = Carbon::today();
        $endDate = $today->copy()->addDays($this->faker->numberBetween(30, 90));
        
        return $this->state(function (array $attributes) use ($today, $endDate) {
            return [
                'start_date' => $today->subDays($this->faker->numberBetween(1, 30))->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ];
        });
    }
}
