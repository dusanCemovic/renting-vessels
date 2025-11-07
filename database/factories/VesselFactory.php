<?php

namespace Database\Factories;

use App\Models\Vessel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vessel>
 */
class VesselFactory extends Factory
{
    protected $model = Vessel::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company(),
            'type' => 'TypeX',
            'size' => 2,
        ];
    }
}
