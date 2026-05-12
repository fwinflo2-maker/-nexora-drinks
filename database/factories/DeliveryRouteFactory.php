<?php

namespace Database\Factories;

use App\Models\DeliveryRoute;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeliveryRoute>
 */
class DeliveryRouteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = fake()->dateTimeBetween('-30 days', '+7 days');

        return [
            'team_id' => Team::factory(),
            'name' => 'Tournée '.fake()->randomElement(['Akwa', 'Bonanjo', 'Deido', 'Bali', 'Makepe']).' - '.$date->format('d/m/Y'),
            'date' => $date,
            'driver_id' => null,
            'vehicle_id' => null,
            'status' => fake()->randomElement(['planned', 'in_progress', 'completed']),
            'total_distance_km' => fake()->optional(0.6)->randomFloat(2, 10, 200),
            'departure_time' => fake()->optional(0.5)->time('H:i:s'),
            'arrival_time' => null,
            'created_by' => User::factory(),
        ];
    }

    /**
     * A planned route.
     */
    public function planned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'planned',
        ]);
    }

    /**
     * A completed route.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }
}
