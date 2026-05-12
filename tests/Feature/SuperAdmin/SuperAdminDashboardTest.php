<?php

use App\Models\Team;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('super admin can access the dashboard', function () {
    $superAdmin = User::factory()->create(['nexora_role' => 'super_admin']);

    $this->actingAs($superAdmin)
        ->get(route('super-admin.dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('super-admin/dashboard')
            ->has('networkKpis')
            ->has('tenants')
            ->has('users')
            ->has('systemHealth')
            ->has('recentAuditLogs')
        );
});

test('non-super-admin gets a 403 on the super admin dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('super-admin.dashboard'))
        ->assertForbidden();
});

test('guest is redirected to login from the super admin dashboard', function () {
    $this->get(route('super-admin.dashboard'))
        ->assertRedirect(route('login'));
});

test('super admin can activate a tenant', function () {
    $superAdmin = User::factory()->create(['nexora_role' => 'super_admin']);
    $team = Team::factory()->create(['is_active' => false]);

    $this->actingAs($superAdmin)
        ->post(route('super-admin.tenants.activate', $team->id))
        ->assertRedirect();

    expect($team->fresh()->is_active)->toBeTrue();
});

test('super admin can suspend a tenant', function () {
    $superAdmin = User::factory()->create(['nexora_role' => 'super_admin']);
    $team = Team::factory()->create(['is_active' => true]);

    $this->actingAs($superAdmin)
        ->post(route('super-admin.tenants.suspend', $team->id))
        ->assertRedirect();

    expect($team->fresh()->is_active)->toBeFalse();
});

test('non-super-admin cannot activate a tenant', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['is_active' => false]);

    $this->actingAs($user)
        ->post(route('super-admin.tenants.activate', $team->id))
        ->assertForbidden();

    expect($team->fresh()->is_active)->toBeFalse();
});

test('non-super-admin cannot suspend a tenant', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['is_active' => true]);

    $this->actingAs($user)
        ->post(route('super-admin.tenants.suspend', $team->id))
        ->assertForbidden();

    expect($team->fresh()->is_active)->toBeTrue();
});
