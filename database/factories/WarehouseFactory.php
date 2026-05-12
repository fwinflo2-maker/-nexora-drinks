<?php

namespace Database\Factories;

use App\Enums\WarehouseType;
use App\Models\Team;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Warehouse>
 */
class WarehouseFactory extends Factory
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
                'Dépôt Central', 'Dépôt Nord', 'Dépôt Sud',
                'Entrepôt Douala', 'Entrepôt Yaoundé', 'Magasin Bafoussam',
            ]),
            'address' => fake()->address(),
            'type' => WarehouseType::Main,
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the warehouse is a truck.
     */
    public function truck(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => WarehouseType::Truck,
            'name' => 'Camion '.fake()->randomElement(['A', 'B', 'C']).'-'.fake()->numberBetween(1, 99),
        ]);
    }

    /**
     * Indicate that the warehouse is secondary.
     */
    public function secondary(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => WarehouseType::Secondary,
        ]);
    }
}
