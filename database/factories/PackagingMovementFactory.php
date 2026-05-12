<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\PackagingMovement;
use App\Models\PackagingType;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PackagingMovement>
 */
class PackagingMovementFactory extends Factory
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
            'client_id' => Client::factory(),
            'packaging_type_id' => PackagingType::factory(),
            'movement_type' => fake()->randomElement(['out', 'in']),
            'quantity' => fake()->numberBetween(1, 20),
            'delivery_id' => null,
            'notes' => fake()->optional(0.3)->sentence(),
            'created_by' => User::factory(),
        ];
    }

    /**
     * A sortie (livraison) movement.
     */
    public function sortie(): static
    {
        return $this->state(fn (array $attributes) => [
            'movement_type' => 'out',
        ]);
    }

    /**
     * A retour (return) movement.
     */
    public function retour(): static
    {
        return $this->state(fn (array $attributes) => [
            'movement_type' => 'in',
        ]);
    }
}
