<?php

use App\Enums\TeamRole;
use App\Models\Drinks\Procurement;

test('ops peut valider un approvisionnement de son équipe', function () {
    [$ops, $team] = drinksMember(TeamRole::Ops);
    $procurement = Procurement::factory()->create(['team_id' => $team->id]);

    expect($ops->can('validate', $procurement))->toBeTrue()
        ->and($ops->can('create', Procurement::class))->toBeTrue()
        ->and($ops->can('viewAny', Procurement::class))->toBeTrue();
});

test('caissier ne peut pas valider ni créer un approvisionnement', function () {
    [$caissier, $team] = drinksMember(TeamRole::Caissier);
    $procurement = Procurement::factory()->create(['team_id' => $team->id]);

    expect($caissier->can('validate', $procurement))->toBeFalse()
        ->and($caissier->can('create', Procurement::class))->toBeFalse()
        ->and($caissier->can('viewAny', Procurement::class))->toBeFalse();
});

test('gerant peut valider et créer des approvisionnements', function () {
    [$gerant, $team] = drinksMember(TeamRole::Gerant);
    $procurement = Procurement::factory()->create(['team_id' => $team->id]);

    expect($gerant->can('validate', $procurement))->toBeTrue()
        ->and($gerant->can('create', Procurement::class))->toBeTrue();
});
