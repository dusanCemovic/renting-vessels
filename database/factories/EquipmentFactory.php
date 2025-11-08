<?php

namespace Database\Factories;

use App\Models\Equipment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Equipment>
 */
class EquipmentFactory extends Factory
{
    protected $model = Equipment::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('??#')),
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->optional()->sentence(),
        ];
    }
}
