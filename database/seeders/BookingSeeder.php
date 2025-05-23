<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Booking;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $activities = Activity::all();
        
        if ($activities->isEmpty()) {
            $this->command->info('No activities found. Please run ActivitySeeder first.');
            return;
        }

        $this->command->info('Creating bookings...');

        // Crear Bookings específicas para hoy y próximos días
        $this->createBookingsForToday($activities);
        
        // Crear Bookings históricas
        $this->createHistoricalBookings($activities);
        
        // Crear Bookings futuras
        $this->createFutureBookings($activities);
        
        // Crear algunas Bookings adicionales aleatorias
        $this->command->info('Creating random bookings...');
        Booking::factory()->count(25)->create();
        
        // Crear Bookings para grupos grandes
        $this->command->info('Creating large group bookings...');
        Booking::factory()->count(8)->largGroup()->create();
        
        // Crear Bookings recientes
        $this->command->info('Creating recent bookings...');
        Booking::factory()->count(12)->recentBooking()->create();
        
        $totalBookings = Booking::count();
        $this->command->info("Created {$totalBookings} bookings successfully!");
    }

    /**
     * Crear Bookings para hoy
     */
    private function createBookingsForToday($activities)
    {
        $this->command->info('Creating bookings for today...');
        $today = Carbon::today();
        
        // Seleccionar Activityes disponibles hoy
        $availableToday = $activities->filter(function ($activity) use ($today) {
            return $today->between(Carbon::parse($activity->start_date), Carbon::parse($activity->end_date));
        });

        if ($availableToday->isEmpty()) {
            $this->command->info('No activities available today. Skipping today bookings.');
            return;
        }

        // Crear 10-15 Bookings para hoy
        $bookingsCount = rand(10, 15);
        
        for ($i = 0; $i < $bookingsCount; $i++) {
            $activity = $availableToday->random();
            $numberOfPeople = rand(1, 6);
            
            Booking::create([
                'activity_id' => $activity->id,
                'number_of_people' => $numberOfPeople,
                'booking_price' => $activity->price_per_person * $numberOfPeople,
                'booking_date' => Carbon::now()->subHours(rand(1, 48))->format('Y-m-d H:i:s'),
                'activity_date' => $today->format('Y-m-d'),
            ]);
        }
        
        $this->command->info("Created {$bookingsCount} bookings for today.");
    }

    /**
     * Crear Bookings históricas
     */
    private function createHistoricalBookings($activities)
    {
        $this->command->info('Creating historical bookings...');
        
        // Crear Bookings de los últimos 3 meses
        $historicalBookings = 0;
        
        for ($i = 0; $i < 40; $i++) {
            $activity = $activities->random();
            $numberOfPeople = rand(1, 8);
            
            // Fecha de Activity en los últimos 3 meses
            $activityDate = Carbon::now()->subDays(rand(1, 90));
            
            // Asegurar que la fecha está dentro del rango de la Activity
            $startDate = Carbon::parse($activity->start_date);
            $endDate = Carbon::parse($activity->end_date);
            
            if ($activityDate->between($startDate, $endDate)) {
                $bookingDate = $activityDate->copy()->subDays(rand(1, 30));
                
                // Asegurar que la fecha de Booking no sea futura
                if ($bookingDate->lte(Carbon::now())) {
                    Booking::create([
                        'activity_id' => $activity->id,
                        'number_of_people' => $numberOfPeople,
                        'booking_price' => $activity->price_per_person * $numberOfPeople,
                        'booking_date' => $bookingDate->format('Y-m-d H:i:s'),
                        'activity_date' => $activityDate->format('Y-m-d'),
                    ]);
                    $historicalBookings++;
                }
            }
        }
        
        $this->command->info("Created {$historicalBookings} historical bookings.");
    }

    /**
     * Crear Bookings futuras
     */
    private function createFutureBookings($activities)
    {
        $this->command->info('Creating future bookings...');
        
        // Crear Bookings para próximas fechas
        $futureBookings = 0;
        
        for ($i = 0; $i < 30; $i++) {
            $activity = $activities->random();
            $numberOfPeople = rand(1, 6);
            
            // Fecha de Activity en los próximos 2 meses
            $activityDate = Carbon::now()->addDays(rand(1, 60));
            
            // Asegurar que la fecha está dentro del rango de la Activity
            $startDate = Carbon::parse($activity->start_date);
            $endDate = Carbon::parse($activity->end_date);
            
            if ($activityDate->between($startDate, $endDate)) {
                $bookingDate = Carbon::now()->subHours(rand(1, 24));
                
                Booking::create([
                    'activity_id' => $activity->id,
                    'number_of_people' => $numberOfPeople,
                    'booking_price' => $activity->price_per_person * $numberOfPeople,
                    'booking_date' => $bookingDate->format('Y-m-d H:i:s'),
                    'activity_date' => $activityDate->format('Y-m-d'),
                ]);
                $futureBookings++;
            }
        }
        
        $this->command->info("Created {$futureBookings} future bookings.");
    }

    /**
     * Crear Bookings específicas para testing
     */
    private function createSpecificTestBookings($activities)
    {
        $this->command->info('Creating specific test bookings...');
        
        // Crear algunas Bookings específicas para fechas conocidas
        $testDates = [
            Carbon::today()->format('Y-m-d'),
            Carbon::today()->addDay()->format('Y-m-d'),
            Carbon::today()->addDays(2)->format('Y-m-d'),
            Carbon::today()->addWeek()->format('Y-m-d'),
        ];

        foreach ($testDates as $date) {
            $availableActivities = $activities->filter(function ($activity) use ($date) {
                return Carbon::parse($date)->between(
                    Carbon::parse($activity->start_date),
                    Carbon::parse($activity->end_date)
                );
            });

            if ($availableActivities->isNotEmpty()) {
                // Crear 2-4 Bookings para cada fecha de prueba
                $bookingsForDate = rand(2, 4);
                
                for ($i = 0; $i < $bookingsForDate; $i++) {
                    $activity = $availableActivities->random();
                    $numberOfPeople = rand(1, 5);
                    
                    Booking::create([
                        'activity_id' => $activity->id,
                        'number_of_people' => $numberOfPeople,
                        'booking_price' => $activity->price_per_person * $numberOfPeople,
                        'booking_date' => Carbon::now()->subHours(rand(1, 72))->format('Y-m-d H:i:s'),
                        'activity_date' => $date,
                    ]);
                }
            }
        }
    }

    /**
     * Crear Bookings para Activityes populares
     */
    private function createBookingsForPopularActivities($activities)
    {
        $this->command->info('Creating bookings for popular activities...');
        
        $popularActivities = $activities->where('popularity', '>', 80);
        
        foreach ($popularActivities as $activity) {
            // Las Activityes populares tienen más Bookings
            $numberOfBookings = rand(3, 8);
            
            for ($i = 0; $i < $numberOfBookings; $i++) {
                // Fecha aleatoria dentro del rango de la Activity
                $startDate = Carbon::parse($activity->start_date);
                $endDate = Carbon::parse($activity->end_date);
                
                $daysDiff = $startDate->diffInDays($endDate);
                if ($daysDiff > 0) {
                    $activityDate = $startDate->copy()->addDays(rand(0, $daysDiff));
                    
                    // Solo crear si la fecha no es muy pasada
                    if ($activityDate->gte(Carbon::now()->subMonths(2))) {
                        $bookingDate = $activityDate->copy()->subDays(rand(1, 15));
                        
                        // Asegurar que la fecha de Booking no sea futura
                        if ($bookingDate->lte(Carbon::now())) {
                            $numberOfPeople = rand(1, 6);
                            
                            Booking::create([
                                'activity_id' => $activity->id,
                                'number_of_people' => $numberOfPeople,
                                'booking_price' => $activity->price_per_person * $numberOfPeople,
                                'booking_date' => $bookingDate->format('Y-m-d H:i:s'),
                                'activity_date' => $activityDate->format('Y-m-d'),
                            ]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Crear Bookings distribuidas a lo largo del tiempo
     */
    private function createDistributedBookings($activities)
    {
        $this->command->info('Creating distributed bookings...');
        
        // Crear Bookings distribuidas en diferentes meses
        $months = [
            Carbon::now()->subMonths(2),
            Carbon::now()->subMonth(),
            Carbon::now(),
            Carbon::now()->addMonth(),
            Carbon::now()->addMonths(2),
        ];

        foreach ($months as $month) {
            $bookingsForMonth = rand(5, 12);
            
            for ($i = 0; $i < $bookingsForMonth; $i++) {
                $activity = $activities->random();
                
                // Fecha de Activity en el mes actual
                $activityDate = $month->copy()->addDays(rand(1, $month->daysInMonth - 1));
                
                // Verificar que esté en el rango de la Activity
                if ($activityDate->between(
                    Carbon::parse($activity->start_date),
                    Carbon::parse($activity->end_date)
                )) {
                    $bookingDate = $activityDate->copy()->subDays(rand(1, 20));
                    
                    // Solo crear si la fecha de Booking no es futura
                    if ($bookingDate->lte(Carbon::now())) {
                        $numberOfPeople = rand(1, 7);
                        
                        Booking::create([
                            'activity_id' => $activity->id,
                            'number_of_people' => $numberOfPeople,
                            'booking_price' => $activity->price_per_person * $numberOfPeople,
                            'booking_date' => $bookingDate->format('Y-m-d H:i:s'),
                            'activity_date' => $activityDate->format('Y-m-d'),
                        ]);
                    }
                }
            }
        }
    }
}
