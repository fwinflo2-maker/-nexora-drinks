<?php

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $team = Team::factory()->create(['name' => 'Guest Enterprise']);

    $response = $this->get("/{$team->slug}/dashboard");

    $response->assertRedirect(route('login'));
});

test('authenticated team owners see the admin dashboard', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $this->actingAs($user)
        ->get("/{$team->slug}/dashboard")
        ->assertRedirect("/{$team->slug}/drinks/dashboard");
});

test('authenticated team employees see a business-specific dashboard', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['name' => 'Entreprise Commerciale']);

    $team->members()->attach($user, ['role' => TeamRole::Commercial->value]);
    $user->switchTeam($team);

    $this->actingAs($user)
        ->get("/{$team->slug}/drinks/dashboard")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('drinks/dashboard')
            ->where('currentTeam.role', TeamRole::Commercial->value)
        );
});

test('super admin is redirected to the dedicated super-admin dashboard', function () {
    $user = User::factory()->create(['nexora_role' => 'super_admin']);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertRedirect(route('super-admin.dashboard'));

    $this->actingAs($user)
        ->get(route('super-admin.dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('super-admin/dashboard')
        );
});
