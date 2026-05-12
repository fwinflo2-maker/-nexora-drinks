<?php

namespace Database\Factories;

use App\Models\Supplier;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
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
            'name' => fake()->randomElement([
                'SABC - Société Anonyme des Brasseries du Cameroun',
                'UCB - Union Camerounaise de Brasseries',
                'Guinness Cameroun S.A.',
                'Source du Pays',
                'Coca-Cola Cameroun',
                'Tangui - Société des Eaux Minérales',
                'SOBEBRA',
                'Brasseries du Tchad',
            ]),
            'phone' => fake()->numerify('+237 2## ### ###'),
            'email' => fake()->companyEmail(),
            'address' => fake()->address(),
            'payment_terms_days' => fake()->randomElement([15, 30, 45, 60]),
            'is_active' => true,
        ];
    }
}
