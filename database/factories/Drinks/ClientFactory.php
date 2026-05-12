<?php

namespace Database\Factories\Drinks;

use App\Models\Drinks\Client;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'code' => fake()->unique()->bothify('CLT-#####'),
            'name' => fake()->company(),
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'contact' => fake()->name(),
            'pickup_fee' => fake()->numberBetween(0, 2000),
            'pickup_fee_pet' => fake()->numberBetween(0, 1000),
            'credit_limit' => fake()->numberBetween(0, 1000000),
            'is_active' => true,
        ];
    }
}
