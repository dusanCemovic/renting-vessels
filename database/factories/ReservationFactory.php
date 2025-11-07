<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\Vessel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->word(),
            'description' => $this->faker->unique()->words(10),
            'vessel_id' => Vessel::factory(),
            'start_at' => $this->faker->dateTimeBetween('+1 days', '+2 days'),
            'end_at' => '',
            'required_equipment' => '',
            'status' => $this->faker->randomElement([20, 25, 30, 35, 40]),
        ];
    }
}
