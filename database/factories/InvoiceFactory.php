<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $sequence = 1;

        $subtotal = fake()->numberBetween(5000, 1000000);
        $taxAmount = round($subtotal * 0.1925, 2);
        $total = $subtotal + $taxAmount;

        return [
            'team_id' => Team::factory(),
            'invoice_number' => 'FAC-'.date('Y').'-'.str_pad((string) $sequence++, 5, '0', STR_PAD_LEFT),
            'client_id' => Client::factory(),
            'order_id' => null,
            'type' => 'invoice',
            'status' => fake()->randomElement(['draft', 'sent', 'paid', 'partial', 'overdue']),
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'paid_amount' => 0,
            'due_date' => fake()->optional(0.8)->dateTimeBetween('now', '+30 days'),
            'pdf_path' => null,
            'sent_at' => null,
            'created_by' => User::factory(),
        ];
    }

    /**
     * A paid invoice.
     */
    public function paid(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'paid',
                'paid_amount' => $attributes['total'],
            ];
        });
    }
}
