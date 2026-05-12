<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ── Super Administrateur NEXORA ──────────────────────────────────
        User::factory()->create([
            'name' => 'Super Admin NEXORA',
            'email' => 'superadmin@nexora.app',
            'password' => Hash::make('nexora_admin2026'),
            'nexora_role' => 'super_admin',
            'email_verified_at' => now(),
        ]);

        // ── Compte de test standard ──────────────────────────────────────
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('nexora_admin2026'),
            'email_verified_at' => now(),
        ]);
    }
}
