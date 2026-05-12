<?php

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Find a super admin
$admin = User::where('nexora_role', 'super_admin')->first();

if (!$admin) {
    echo "No super admin found.\n";
    exit;
}

// Find a team
$team = Team::first();

if (!$team) {
    echo "No team found.\n";
    exit;
}

echo "Testing suspend for team {$team->id} as user {$admin->id} ({$admin->nexora_role})\n";

Auth::login($admin);

$request = Request::create("/super-admin/tenants/{$team->id}/suspend", 'POST');
$request->setLaravelSession($app['session']->driver());

try {
    $response = $kernel->handle($request);
    echo "Response status: " . $response->getStatusCode() . "\n";
    if ($response->getStatusCode() == 403) {
        echo "403 Forbidden detected!\n";
    }
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
