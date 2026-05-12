<?php

use App\Enums\TeamRole;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ── index() ───────────────────────────────────────────────────────────────────

test('owner peut voir les membres de son équipe', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $this->actingAs($user)
        ->get(route('equipe.index', ['current_team' => $team->slug]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('equipe/index')
            ->has('team')
            ->has('members')
            ->has('roles')
        );
});

test('non-membre ne peut pas voir l\'équipe', function () {
    $owner = User::factory()->create();
    $team = $owner->currentTeam;
    $outsider = User::factory()->create();

    $this->actingAs($outsider)
        ->get(route('equipe.index', ['current_team' => $team->slug]))
        ->assertForbidden();
});

// ── store() ───────────────────────────────────────────────────────────────────

test('owner peut ajouter un membre', function () {
    $owner = User::factory()->create();
    $team = $owner->currentTeam;
    $newMember = User::factory()->create();

    $this->actingAs($owner)
        ->post(route('equipe.store', ['current_team' => $team->slug]), [
            'email' => $newMember->email,
            'role' => TeamRole::Member->value,
        ])
        ->assertRedirect();

    expect($team->members()->where('user_id', $newMember->id)->exists())->toBeTrue();
});

test('ne peut pas ajouter un utilisateur déjà membre', function () {
    $owner = User::factory()->create();
    $team = $owner->currentTeam;
    $existingMember = User::factory()->create();
    $team->members()->attach($existingMember->id, ['role' => TeamRole::Member->value]);

    $this->actingAs($owner)
        ->post(route('equipe.store', ['current_team' => $team->slug]), [
            'email' => $existingMember->email,
            'role' => TeamRole::Member->value,
        ])
        ->assertSessionHasErrors('email');
});

// ── update() ──────────────────────────────────────────────────────────────────

test('owner peut changer le rôle d\'un membre', function () {
    $owner = User::factory()->create();
    $team = $owner->currentTeam;
    $member = User::factory()->create();
    $team->members()->attach($member->id, ['role' => TeamRole::Member->value]);

    $membership = Membership::where('team_id', $team->id)
        ->where('user_id', $member->id)
        ->first();

    $this->actingAs($owner)
        ->patch(route('equipe.update', ['current_team' => $team->slug, 'membership' => $membership->id]), [
            'role' => TeamRole::Manager->value,
        ])
        ->assertRedirect();

    $membership->refresh();
    expect($membership->role)->toBe(TeamRole::Manager);
});

// ── destroy() ─────────────────────────────────────────────────────────────────

test('owner peut retirer un membre', function () {
    $owner = User::factory()->create();
    $team = $owner->currentTeam;
    $member = User::factory()->create();
    $team->members()->attach($member->id, ['role' => TeamRole::Member->value]);

    $membership = Membership::where('team_id', $team->id)
        ->where('user_id', $member->id)
        ->first();

    $this->actingAs($owner)
        ->delete(route('equipe.destroy', ['current_team' => $team->slug, 'membership' => $membership->id]))
        ->assertRedirect();

    expect($team->members()->where('user_id', $member->id)->exists())->toBeFalse();
});

test('ne peut pas retirer le owner de l\'équipe', function () {
    $owner = User::factory()->create();
    $team = $owner->currentTeam;

    $ownerMembership = Membership::where('team_id', $team->id)
        ->where('user_id', $owner->id)
        ->first();

    $this->actingAs($owner)
        ->delete(route('equipe.destroy', ['current_team' => $team->slug, 'membership' => $ownerMembership->id]))
        ->assertForbidden();
});

// ── poste & extra_roles ────────────────────────────────────────────────────

test('store enregistre poste et extra_roles dans le pivot', function () {
    $owner = User::factory()->create();
    $team = $owner->currentTeam;
    $newMember = User::factory()->create();

    $this->actingAs($owner)
        ->post(route('equipe.store', ['current_team' => $team->slug]), [
            'email' => $newMember->email,
            'role' => 'commercial',
            'poste' => 'Responsable dépôt',
            'extra_roles' => ['logisticien', 'magasinier'],
        ])
        ->assertRedirect();

    $membership = $team->members()->where('user_id', $newMember->id)->withPivot('poste', 'extra_roles')->first();
    expect($membership->pivot->poste)->toBe('Responsable dépôt');
    expect($membership->pivot->extra_roles)->toContain('logisticien');
});

test('update modifie poste et extra_roles', function () {
    $owner = User::factory()->create();
    $team = $owner->currentTeam;
    $member = User::factory()->create();
    $team->members()->attach($member->id, ['role' => 'commercial', 'poste' => 'Ancien poste']);

    $membership = Membership::where('team_id', $team->id)->where('user_id', $member->id)->first();

    $this->actingAs($owner)
        ->patch(route('equipe.update', ['current_team' => $team->slug, 'membership' => $membership->id]), [
            'role' => 'commercial',
            'poste' => 'Nouveau poste',
            'extra_roles' => ['comptable'],
        ])
        ->assertRedirect();

    $membership->refresh();
    expect($membership->poste)->toBe('Nouveau poste');
    expect($membership->extra_roles)->toContain('comptable');
});
