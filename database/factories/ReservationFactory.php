<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\Vessel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        // Generate a coherent window where end_at is after start_at
        $start = $this->faker->dateTimeBetween('+1 days', '+2 days');
        $durationHours = $this->faker->numberBetween(1, 4);
        $end = (clone $start)->modify("+{$durationHours} hours");

        return [
            'title' => $this->faker->unique()->sentence(3),
            'description' => $this->faker->sentence(8),
            'vessel_id' => Vessel::factory(),
            'start_at' => $start,
            'end_at' => $end,
            'required_equipment' => [],
            'status' => 'scheduled',
        ];
    }
}
