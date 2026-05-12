<?php

namespace Database\Factories\Drinks;

use App\Enums\Drinks\TransactionStatus;
use App\Models\Drinks\CashInput;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CashInput>
 */
class CashInputFactory extends Factory
{
    protected $model = CashInput::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'code' => fake()->unique()->bothify('APR-#####'),
            'amount' => fake()->numberBetween(5000, 500000),
            'document_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'label' => fake()->word(),
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
