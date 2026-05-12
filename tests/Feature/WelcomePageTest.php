<?php

use App\Models\User;

test('welcome page displays sector selector with all three sectors', function () {
    $response = $this->get('/');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('welcome')
        );
});

test('authenticated user sees dashboard link on welcome page', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $this->actingAs($user)
        ->get('/')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('welcome')
            ->has('auth.user')
            ->has('currentTeam')
        );
});

test('super admin sees super-admin dashboard link on welcome page', function () {
    $user = User::factory()->create(['nexora_role' => 'super_admin']);

    $this->actingAs($user)
        ->get('/')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('welcome')
            ->where('auth.user.nexora_role', 'super_admin')
        );
});

test('unauthenticated user sees registration/login links on welcome page', function () {
    $response = $this->get('/');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('welcome')
            ->where('auth.user', null)
        );
});
