<?php

use App\Models\User;
use Illuminate\Support\Facades\Cache;

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    // OTP must be verified before registration
    Cache::put('otp_verified_test@example.com', true, 600);

    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'company_name' => 'Test Company',
    ]);

    $this->assertAuthenticated();

    $user = User::where('email', 'test@example.com')->first();
    expect($user)->not->toBeNull();

    // Team is inactive until activated by super admin
    $response->assertRedirect(route('pending-approval'));
});
