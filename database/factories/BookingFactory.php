<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition()
    {
        $activity = Activity::inRandomOrder()->first();
        
        // Si no hay Activityes, crear una
        if (!$activity) {
            $activity = Activity::factory()->create();
        }

        $numberOfPeople = $this->faker->numberBetween(1, 8);
        $bookingPrice = $activity->price_per_person * $numberOfPeople;
        
        // Fecha de realización dentro del rango de disponibilidad de la Activity
        $activityDate = $this->faker->dateTimeBetween(
            $activity->start_date, 
            $activity->end_date
        );

        // Fecha de Booking anterior a la fecha de realización
        $bookingDate = $this->faker->dateTimeBetween(
            '-2 months', 
            $activityDate
        );

        return [
            'activity_id' => $activity->id,
            'number_of_people' => $numberOfPeople,
            'booking_price' => $bookingPrice,
            'booking_date' => $bookingDate->format('Y-m-d H:i:s'),
            'activity_date' => $activityDate->format('Y-m-d'),
        ];
    }

    public function forActivity(Activity $activity)
    {
        return $this->state(function (array $attributes) use ($activity) {
            $numberOfPeople = $this->faker->numberBetween(1, 8);
            $bookingPrice = $activity->price_per_person * $numberOfPeople;
            
            $activityDate = $this->faker->dateTimeBetween(
                $activity->start_date, 
                $activity->end_date
            );

            $bookingDate = $this->faker->dateTimeBetween(
                '-2 months', 
                $activityDate
            );

            return [
                'activity_id' => $activity->id,
                'number_of_people' => $numberOfPeople,
                'booking_price' => $bookingPrice,
                'booking_date' => $bookingDate->format('Y-m-d H:i:s'),
                'activity_date' => $activityDate->format('Y-m-d'),
            ];
        });
    }

    public function forToday()
    {
        return $this->state(function (array $attributes) {
            return [
                'activity_date' => Carbon::today()->format('Y-m-d'),
            ];
        });
    }

    public function largGroup()
    {
        return $this->state(function (array $attributes) {
            $numberOfPeople = $this->faker->numberBetween(5, 15);
            $activity = Activity::find($attributes['activity_id']);
            
            return [
                'number_of_people' => $numberOfPeople,
                'booking_price' => $activity->price_per_person * $numberOfPeople,
            ];
        });
    }

    public function recentBooking()
    {
        return $this->state(function (array $attributes) {
            return [
                'booking_date' => $this->faker->dateTimeBetween('-1 week', 'now')->format('Y-m-d H:i:s'),
            ];
        });
    }
}
