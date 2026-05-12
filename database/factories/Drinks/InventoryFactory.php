<?php

namespace Database\Factories\Drinks;

use App\Enums\Drinks\TransactionStatus;
use App\Models\Drinks\Inventory;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Inventory>
 */
class InventoryFactory extends Factory
{
    protected $model = Inventory::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'code' => fake()->unique()->bothify('INV-#####'),
            'document_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'observation' => fake()->optional()->sentence(),
            'status' => TransactionStatus::Draft,
            'validated_at' => null,
            'validated_by' => null,
            'created_by' => User::factory(),
        ];
    }

    public function validated(): static
    {
        return $this->state(fn () => [
            'status' => TransactionStatus::Validated,
            'validated_at' => now(),
        ]);
    }
}
