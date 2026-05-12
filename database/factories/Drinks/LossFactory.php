<?php

namespace Database\Factories\Drinks;

use App\Enums\Drinks\TransactionStatus;
use App\Models\Drinks\Loss;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Loss>
 */
class LossFactory extends Factory
{
    protected $model = Loss::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'code' => fake()->unique()->bothify('PTE-#####'),
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
