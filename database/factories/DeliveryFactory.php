<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Delivery;
use App\Models\DeliveryRoute;
use App\Models\Order;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Delivery>
 */
class DeliveryFactory extends Factory
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
            'route_id' => DeliveryRoute::factory(),
            'order_id' => Order::factory(),
            'client_id' => Client::factory(),
            'status' => fake()->randomElement(['pending', 'delivered', 'partial', 'failed']),
            'sequence_number' => fake()->numberBetween(1, 20),
            'delivered_at' => null,
            'signature_path' => null,
            'notes' => fake()->optional(0.2)->sentence(),
        ];
    }

    /**
     * A delivered delivery.
     */
    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }
}
