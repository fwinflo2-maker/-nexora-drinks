<?php

use App\Enums\TeamRole;
use App\Models\Drinks\Sale;

test('caissier peut créer et valider des ventes', function () {
    [$caissier, $team] = drinksMember(TeamRole::Caissier);
    $sale = Sale::factory()->create(['team_id' => $team->id]);

    expect($caissier->can('create', Sale::class))->toBeTrue()
        ->and($caissier->can('validate', $sale))->toBeTrue()
        ->and($caissier->can('viewAny', Sale::class))->toBeTrue();
});

test('magasinier ne peut pas créer ni voir les ventes', function () {
    [$magasinier] = drinksMember(TeamRole::Magasinier);

    expect($magasinier->can('create', Sale::class))->toBeFalse()
        ->and($magasinier->can('viewAny', Sale::class))->toBeFalse();
});

test('ops ne peut pas créer de ventes', function () {
    [$ops] = drinksMember(TeamRole::Ops);

    expect($ops->can('create', Sale::class))->toBeFalse()
        ->and($ops->can('viewAny', Sale::class))->toBeFalse();
});
