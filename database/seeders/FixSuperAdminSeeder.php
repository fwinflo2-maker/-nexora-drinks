<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Corrige l'état du Super Admin en base de données :
 * - current_team_id mis à NULL (le super admin n'appartient à aucune équipe)
 * - Vérifie que nexora_role = 'super_admin'
 */
class FixSuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::where('nexora_role', 'super_admin')->first();

        if (! $superAdmin) {
            $this->command->error('Aucun super admin trouvé !');

            return;
        }

        // Le super admin ne doit pas avoir de current_team_id
        // pour éviter toute redirection vers un slug d'équipe
        $superAdmin->update(['current_team_id' => null]);

        $this->command->info('✅ Super Admin corrigé :');
        $this->command->table(
            ['ID', 'Nom', 'Email', 'Rôle', 'current_team_id'],
            [[
                $superAdmin->id,
                $superAdmin->name,
                $superAdmin->email,
                $superAdmin->nexora_role,
                $superAdmin->fresh()->current_team_id ?? 'NULL ✅',
            ]]
        );

        // Vérification globale
        $this->command->info('');
        $this->command->info('📊 État de la base de données :');
        $this->command->table(
            ['Métrique', 'Valeur'],
            [
                ['Utilisateurs total', User::count()],
                ['Super admins', User::where('nexora_role', 'super_admin')->count()],
                ['Teams total', DB::table('teams')->count()],
                ['Teams actives', DB::table('teams')->where('is_active', true)->count()],
                ['Teams inactives', DB::table('teams')->where('is_active', false)->count()],
                ['Members (pivot)', DB::table('team_members')->count()],
                ['Sessions actives', DB::table('sessions')->count()],
            ]
        );
    }
}
