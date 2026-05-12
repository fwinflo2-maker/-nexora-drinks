<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'name' => fake()->randomElement([
                'Camion 1', 'Camion 2', 'Toyota Hilux', 'Mercedes Sprinter',
                'Ford Transit', 'Mitsubishi Canter', 'Isuzu NPR',
            ]),
            'plate' => strtoupper(fake()->bothify('LT-###-??')),
            'capacity_cases' => fake()->randomElement([50, 100, 150, 200, 300]),
            'is_active' => true,
            'driver_id' => null,
        ];
    }

    /**
     * An inactive vehicle.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
