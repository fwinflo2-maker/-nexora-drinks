<?php

use App\Enums\TeamRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeAdminWithTeam(): array
{
    $owner = User::factory()->create();
    $team = $owner->currentTeam;
    $admin = User::factory()->create();
    $team->members()->attach($admin->id, ['role' => TeamRole::Admin->value]);

    return [$admin, $team];
}

// ── index ─────────────────────────────────────────────────────────────────────

test('admin peut voir les membres', function () {
    [$admin, $team] = makeAdminWithTeam();

    $this->actingAs($admin)
        ->get(route('drinks.membres.index', ['current_team' => $team->slug]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('drinks/dashboard')
            ->has('members')
            ->has('roles')
        );
});

test('non-admin reçoit 403 sur index', function () {
    $owner = User::factory()->create();
    $team = $owner->currentTeam;
    $member = User::factory()->create();
    $team->members()->attach($member->id, ['role' => TeamRole::Caissier->value]);

    $this->actingAs($member)
        ->get(route('drinks.membres.index', ['current_team' => $team->slug]))
        ->assertForbidden();
});

// ── store ─────────────────────────────────────────────────────────────────────

test('admin peut créer un profil et l\'ajouter à l\'équipe', function () {
    [$admin, $team] = makeAdminWithTeam();

    $this->actingAs($admin)
        ->post(route('drinks.membres.store', ['current_team' => $team->slug]), [
            'name' => 'Nouveau Membre',
            'email' => 'nouveau@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => TeamRole::Caissier->value,
        ])
        ->assertRedirect();

    $user = User::where('email', 'nouveau@test.com')->first();
    expect($user)->not->toBeNull();
    expect($team->members()->where('user_id', $user->id)->exists())->toBeTrue();
});

test('store échoue si email déjà utilisé', function () {
    [$admin, $team] = makeAdminWithTeam();
    $existing = User::factory()->create(['email' => 'existe@test.com']);

    $this->actingAs($admin)
        ->post(route('drinks.membres.store', ['current_team' => $team->slug]), [
            'name' => 'Test',
            'email' => 'existe@test.com',
            'password' => 'password123',
            'role' => TeamRole::Caissier->value,
        ])
        ->assertSessionHasErrors('email');
});

// ── block / unblock ───────────────────────────────────────────────────────────

test('admin peut bloquer un profil', function () {
    [$admin, $team] = makeAdminWithTeam();
    $member = User::factory()->create();
    $team->members()->attach($member->id, ['role' => TeamRole::Caissier->value]);

    $this->actingAs($admin)
        ->patch(route('drinks.membres.block', ['current_team' => $team->slug, 'user' => $member->id]))
        ->assertRedirect();

    expect($member->fresh()->blocked_at)->not->toBeNull();
});

test('admin peut débloquer un profil', function () {
    [$admin, $team] = makeAdminWithTeam();
    $member = User::factory()->create(['blocked_at' => now()]);
    $team->members()->attach($member->id, ['role' => TeamRole::Caissier->value]);

    $this->actingAs($admin)
        ->patch(route('drinks.membres.unblock', ['current_team' => $team->slug, 'user' => $member->id]))
        ->assertRedirect();

    expect($member->fresh()->blocked_at)->toBeNull();
});

test('admin ne peut pas bloquer le propriétaire', function () {
    [$admin, $team] = makeAdminWithTeam();
    $owner = $team->members()->wherePivot('role', TeamRole::Owner->value)->first();

    $this->actingAs($admin)
        ->patch(route('drinks.membres.block', ['current_team' => $team->slug, 'user' => $owner->id]))
        ->assertSessionHasErrors('block');
});

// ── updatePassword ────────────────────────────────────────────────────────────

test('admin peut changer le mot de passe d\'un membre', function () {
    [$admin, $team] = makeAdminWithTeam();
    $member = User::factory()->create();
    $team->members()->attach($member->id, ['role' => TeamRole::Caissier->value]);

    $this->actingAs($admin)
        ->patch(route('drinks.membres.update-password', ['current_team' => $team->slug, 'user' => $member->id]), [
            'password' => 'newpassword123',
        ])
        ->assertRedirect();
});

// ── updateProfile ─────────────────────────────────────────────────────────────

test('admin peut modifier le nom et email d\'un membre', function () {
    [$admin, $team] = makeAdminWithTeam();
    $member = User::factory()->create();
    $team->members()->attach($member->id, ['role' => TeamRole::Caissier->value]);

    $this->actingAs($admin)
        ->patch(route('drinks.membres.update-profile', ['current_team' => $team->slug, 'user' => $member->id]), [
            'name' => 'Nouveau Nom',
            'email' => 'newemail@test.com',
        ])
        ->assertRedirect();

    expect($member->fresh()->name)->toBe('Nouveau Nom');
    expect($member->fresh()->email)->toBe('newemail@test.com');
});

// ── blocked login ─────────────────────────────────────────────────────────────

test('un profil bloqué ne peut pas se connecter', function () {
    $user = User::factory()->create([
        'email' => 'blocked@test.com',
        'blocked_at' => now(),
    ]);

    $this->post(route('login.store'), [
        'email' => 'blocked@test.com',
        'password' => 'password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('un profil non bloqué peut se connecter', function () {
    $user = User::factory()->create([
        'email' => 'active@test.com',
        'blocked_at' => null,
    ]);

    $this->post(route('login.store'), [
        'email' => 'active@test.com',
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
});
