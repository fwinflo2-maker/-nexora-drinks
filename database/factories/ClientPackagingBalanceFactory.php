<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\ClientPackagingBalance;
use App\Models\PackagingType;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClientPackagingBalance>
 */
class ClientPackagingBalanceFactory extends Factory
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
            'quantity_owed' => fake()->numberBetween(0, 50),
            'last_updated_at' => fake()->optional(0.8)->dateTimeBetween('-30 days', 'now'),
        ];
    }

    /**
     * A balance with no debt.
     */
    public function cleared(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity_owed' => 0,
        ]);
    }
}
