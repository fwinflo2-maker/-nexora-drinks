<?php

namespace Database\Seeders;

use App\Enums\TeamRole;
use App\Models\Drinks\Category;
use App\Models\Drinks\Setting;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Réinitialise les mots de passe de tous les utilisateurs,
 * crée/répare le super admin, et connecte la team VERTEX
 * à tous les modules Drinks (secteur, catégories, paramètres).
 */
class ResetAndSetupSeeder extends Seeder
{
    private const PASSWORD = 'nexora2026';

    public function run(): void
    {
        // ── 1. SUPER ADMIN ───────────────────────────────────────────────
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@nexora.app'],
            [
                'name' => 'Super Admin NEXORA',
                'password' => Hash::make(self::PASSWORD),
                'nexora_role' => 'super_admin',
                'email_verified_at' => now(),
                'current_team_id' => null,
            ]
        );
        $this->command->info('✅ Super Admin : superadmin@nexora.app / '.self::PASSWORD);

        // ── 2. RESET TOUS LES MOTS DE PASSE ─────────────────────────────
        $hashed = Hash::make(self::PASSWORD);
        $count = User::where('id', '!=', $superAdmin->id)->update([
            'password' => $hashed,
            'email_verified_at' => now(),
        ]);
        $this->command->info("🔑 {$count} mot(s) de passe réinitialisé(s) → ".self::PASSWORD);

        // ── 3. CORRIGER LA TEAM VERTEX ───────────────────────────────────
        $team = Team::where('slug', 'vertex')->first();

        if (! $team) {
            $this->command->error('Team VERTEX introuvable.');
        } else {
            $team->update([
                'sector' => 'boissons',
                'is_active' => true,
                'plan' => 'pro',
            ]);

            // S'assurer que l'admin (id=1) a bien le rôle Admin
            DB::table('team_members')
                ->where('team_id', $team->id)
                ->where('user_id', 1)
                ->update(['role' => TeamRole::Admin->value]);

            $this->command->info("✅ Team VERTEX : secteur='boissons', is_active=true");

            // ── 4. PARAMÈTRES DRINKS ────────────────────────────────────
            $settings = [
                'company_name' => 'VERTEX',
                'currency' => 'XAF',
                'tva_rate' => 19.25,
                'invoice_prefix' => 'VTX',
                'timezone' => 'Africa/Douala',
                'fiscal_year_start' => '01-01',
            ];

            foreach ($settings as $key => $value) {
                Setting::updateOrCreate(
                    ['team_id' => $team->id, 'key' => $key],
                    ['value' => $value]
                );
            }
            $this->command->info('✅ '.count($settings).' paramètres Drinks créés/mis à jour');

            // ── 5. CATÉGORIES DRINKS ────────────────────────────────────
            $categories = [
                ['name' => 'Bières',            'description' => 'Bières locales et importées'],
                ['name' => 'Sodas & Limonades', 'description' => 'Boissons gazeuses sucrées'],
                ['name' => 'Eaux Minérales',    'description' => 'Eaux plates et gazeuses'],
                ['name' => 'Vins & Spiritueux', 'description' => 'Vins, whiskies, gins'],
                ['name' => 'Jus de Fruits',     'description' => 'Jus naturels et nectars'],
                ['name' => 'Énergisants',       'description' => 'Boissons énergisantes'],
            ];

            $created = 0;
            foreach ($categories as $cat) {
                $exists = Category::where('team_id', $team->id)->where('name', $cat['name'])->exists();
                if (! $exists) {
                    Category::create([
                        'team_id' => $team->id,
                        'name' => $cat['name'],
                        'description' => $cat['description'],
                    ]);
                    $created++;
                }
            }

            $total = Category::where('team_id', $team->id)->count();
            $this->command->info("✅ {$created} catégorie(s) créée(s) ({$total} total)");
        }

        // ── 6. RAPPORT FINAL ─────────────────────────────────────────────
        $this->command->newLine();
        $this->command->table(
            ['Compte', 'Email', 'Mot de passe', 'Rôle'],
            User::all()->map(fn ($u) => [
                $u->name,
                $u->email,
                self::PASSWORD,
                $u->nexora_role ?: ($u->teams()->first()?->pivot?->role ?? 'N/A'),
            ])->toArray()
        );
    }
}
