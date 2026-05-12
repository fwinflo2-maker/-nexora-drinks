<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
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
            'category' => fake()->randomElement(['carburant', 'entretien', 'salaires', 'loyer', 'fournitures', 'transport', 'autre']),
            'description' => fake()->optional(0.7)->sentence(),
            'amount' => fake()->numberBetween(1000, 500000),
            'payment_method' => fake()->randomElement(['cash', 'orange_money', 'mtn_momo', 'wave', 'cheque', 'transfer']),
            'receipt_path' => null,
            'date' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'created_by' => User::factory(),
        ];
    }
}
