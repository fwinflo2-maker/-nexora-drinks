<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CleanAndResetSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::where('nexora_role', 'super_admin')->firstOrFail();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('team_members')->truncate();
        DB::table('team_invitations')->truncate();
        DB::table('teams')->truncate();
        DB::table('sessions')->truncate();
        DB::table('password_reset_tokens')->truncate();
        DB::table('godmode_audit_logs')->truncate();
        DB::table('godmode_system_logs')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        User::where('id', '!=', $superAdmin->id)->forceDelete();

        $superAdmin->update([
            'password' => Hash::make('nexora2026'),
            'current_team_id' => null,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);

        $this->command->info('✅ Cleaned: all teams and sub-accounts deleted');
        $this->command->info('✅ Superadmin reset: '.$superAdmin->email.' / nexora2026');
        $this->command->table(
            ['ID', 'Nom', 'Email', 'Rôle', 'current_team_id'],
            [[
                $superAdmin->id,
                $superAdmin->name,
                $superAdmin->email,
                $superAdmin->nexora_role,
                'NULL',
            ]]
        );
        $this->command->info('Users restants: '.User::count());
        $this->command->info('Teams restantes: '.DB::table('teams')->count());
    }
}
