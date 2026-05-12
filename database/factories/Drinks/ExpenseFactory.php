<?php

namespace Database\Factories\Drinks;

use App\Enums\Drinks\TransactionStatus;
use App\Models\Drinks\Expense;
use App\Models\Drinks\ExpenseType;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'expense_type_id' => ExpenseType::factory(),
            'amount' => fake()->numberBetween(5000, 100000),
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
