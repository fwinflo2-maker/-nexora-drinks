<?php

require dirname(__DIR__).'/vendor/autoload.php';
$app = require_once dirname(__DIR__).'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Team;
use App\Models\User;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\DB;

$u = User::where('email', 'hotel_manager@nexora.com')->first();
$t = Team::where('slug', 'nexora-palace')->first();

if (! $u) {
    echo "User not found\n";
}
if (! $t) {
    echo "Team not found\n";
}

if ($u && $t) {
    echo 'User: '.$u->email.' (ID: '.$u->id.")\n";
    echo 'Team: '.$t->slug.' (ID: '.$t->id.")\n";
    echo 'Belongs to team: '.($u->belongsToTeam($t) ? 'YES' : 'NO')."\n";
    echo 'Team Role: '.($u->teamRole($t)?->value ?? 'NONE')."\n";

    $membership = DB::table('team_members')
        ->where('user_id', $u->id)
        ->where('team_id', $t->id)
        ->first();
    echo 'Membership record: '.json_encode($membership)."\n";
}
