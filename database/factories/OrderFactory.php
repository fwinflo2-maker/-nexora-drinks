<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Order;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $sequence = 1;

        return [
            'team_id' => Team::factory(),
            'order_number' => 'NX-'.date('Y').'-'.str_pad((string) $sequence++, 5, '0', STR_PAD_LEFT),
            'client_id' => Client::factory(),
            'channel' => fake()->randomElement(['terrain', 'televente', 'client_direct']),
            'status' => fake()->randomElement(['draft', 'confirmed', 'preparing', 'loaded', 'delivered']),
            'delivery_date' => fake()->optional(0.7)->dateTimeBetween('now', '+7 days'),
            'warehouse_id' => null,
            'commercial_id' => null,
            'notes' => fake()->optional(0.2)->sentence(),
            'subtotal' => $subtotal = fake()->numberBetween(5000, 500000),
            'discount_amount' => 0,
            'total' => $subtotal,
            'created_by' => User::factory(),
            'synced_at' => null,
        ];
    }

    /**
     * A confirmed order.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
        ]);
    }

    /**
     * A delivered order.
     */
    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'delivered',
        ]);
    }
}
