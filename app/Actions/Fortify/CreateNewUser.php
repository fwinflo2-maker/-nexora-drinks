<?php

namespace App\Actions\Fortify;

use App\Actions\Teams\CreateTeam;
use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    public function __construct(private CreateTeam $createTeam)
    {
        //
    }

    /**
     * Validate and create a newly registered user and their company.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'company_name' => ['required', 'string', 'max:255'],
        ])->validate();

        if (! Cache::get('otp_verified_'.$input['email'])) {
            throw ValidationException::withMessages([
                'email' => ["L'adresse email n'a pas été vérifiée par OTP."],
            ]);
        }

        return DB::transaction(function () use ($input) {
            $slug = Str::slug($input['company_name']);
            // Ensure unique slug
            if (Team::where('slug', $slug)->exists()) {
                $slug = $slug.'-'.uniqid();
            }

            // 1. Création de l'entreprise (Team) inactive par défaut
            $team = Team::create([
                'name' => $input['company_name'],
                'slug' => $slug,
                'is_personal' => false,
                'plan' => $input['plan'] ?? 'starter',
                'sector' => $input['company_type'] ?? '',
                'is_active' => false, // Doit être activée par le Super Admin
                'domain' => $slug.'.nexora.app',
                'settings_json' => [
                    'company_type' => $input['company_type'] ?? '',
                    'registration_number' => $input['registration_number'] ?? '',
                    'pays' => $input['pays'] ?? '',
                    'ville' => $input['ville'] ?? '',
                    'telephone' => $input['telephone'] ?? '',
                    'warehouses' => $input['warehouses'] ?? '1',
                    'modules' => $input['modules'] ?? [],
                    'roles_config' => $input['roles'] ?? [],
                    'default_categories' => $input['default_categories'] ?? [],
                    'currency' => $input['currency'] ?? 'EUR',
                ],
            ]);

            // 2. Création de l'admin principal
            $adminUser = User::create([
                'name' => $input['name'] ?? 'Admin '.$input['company_name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'current_team_id' => $team->id,
                'email_verified_at' => now(),
            ]);

            DB::table('team_members')->insert([
                'team_id' => $team->id,
                'user_id' => $adminUser->id,
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Activate selected modules (drinks always included)
            $selectedModules = array_unique(array_merge(['drinks'], (array) ($input['modules'] ?? [])));
            foreach ($selectedModules as $module) {
                $team->activateModule($module, $adminUser);
            }

            // 3. Création du compte employé automatique (Optionnel, juste pour l'exemple)
            $employeeEmail = 'employe@'.$slug.'.com';
            $employeeUser = User::create([
                'name' => 'Employé '.$input['company_name'],
                'email' => $employeeEmail,
                'password' => Hash::make('nexora_admin2026'), // Mot de passe par défaut demandé
                'current_team_id' => $team->id,
            ]);

            DB::table('team_members')->insert([
                'team_id' => $team->id,
                'user_id' => $employeeUser->id,
                'role' => 'commercial',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            try {
                $htmlContent = "<p>Bonjour {$adminUser->name},</p>
<p>Votre inscription sur la plateforme <strong>NEXORA</strong> pour l’entreprise <strong>{$team->name}</strong> a été enregistrée avec succès.</p>
<p>Votre compte est actuellement en cours de vérification et de validation par notre équipe. Dès l’activation de votre espace, vous serez contacté dans les plus brefs délais afin de finaliser votre accès à la plateforme.</p>
<p>Nous vous remercions pour votre confiance et vous souhaitons la bienvenue dans l’univers NEXORA.</p>
<p><strong>Cordialement,</strong><br><em>L’équipe NEXORA</em></p>";

                Mail::html($htmlContent, function ($message) use ($adminUser) {
                    $message->to($adminUser->email)
                        ->subject('Confirmation de votre inscription sur NEXORA');
                });
            } catch (\Exception $e) {
                // Silently ignore mail failures during dev/if not configured
                Log::error('Mail failed: '.$e->getMessage());
            }

            return $adminUser;
        });
    }
}
