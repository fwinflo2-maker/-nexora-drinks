<?php

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated users can switch tenant with X-Tenant-ID header', function () {
    $user = User::factory()->create();
    $targetTeam = Team::factory()->create(['name' => 'Acme Distribution']);

    $targetTeam->members()->attach($user, ['role' => TeamRole::Member->value]);

    $this->actingAs($user)
        ->withHeader('X-Tenant-ID', $targetTeam->slug)
        ->get(route('home'))
        ->assertOk();

    expect($user->fresh()->current_team_id)->toBe($targetTeam->id);
});

test('authenticated users cannot switch to tenant they do not belong to', function () {
    $user = User::factory()->create();
    $targetTeam = Team::factory()->create(['name' => 'Unauthorized Tenant']);

    $this->actingAs($user)
        ->withHeader('X-Tenant-ID', $targetTeam->slug)
        ->get(route('home'))
        ->assertForbidden();
});

test('tenant can be resolved from subdomain when no tenant header is sent', function () {
    $user = User::factory()->create();
    $targetTeam = Team::factory()->create(['name' => 'Subdomain Tenant', 'slug' => 'subtenant']);

    $targetTeam->members()->attach($user, ['role' => TeamRole::Member->value]);

    $this->actingAs($user)
        ->get('http://subtenant.nexora.test/')
        ->assertOk();

    expect($user->fresh()->current_team_id)->toBe($targetTeam->id);
});
