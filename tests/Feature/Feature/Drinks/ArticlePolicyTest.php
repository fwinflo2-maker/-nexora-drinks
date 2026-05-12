<?php

use App\Enums\TeamRole;
use App\Models\Drinks\Article;
use App\Models\Team;

test('admin peut effectuer toutes les actions sur les articles', function () {
    [$admin, $team] = drinksMember(TeamRole::Admin);
    $article = Article::factory()->create(['team_id' => $team->id]);

    expect($admin->can('viewAny', Article::class))->toBeTrue()
        ->and($admin->can('view', $article))->toBeTrue()
        ->and($admin->can('create', Article::class))->toBeTrue()
        ->and($admin->can('update', $article))->toBeTrue()
        ->and($admin->can('delete', $article))->toBeTrue();
});

test('caissier peut voir les articles mais pas les créer ni les modifier', function () {
    [$caissier, $team] = drinksMember(TeamRole::Caissier);
    $article = Article::factory()->create(['team_id' => $team->id]);

    expect($caissier->can('viewAny', Article::class))->toBeTrue()
        ->and($caissier->can('view', $article))->toBeTrue()
        ->and($caissier->can('create', Article::class))->toBeFalse()
        ->and($caissier->can('update', $article))->toBeFalse()
        ->and($caissier->can('delete', $article))->toBeFalse();
});

test('member ne peut pas accéder aux articles Drinks', function () {
    [$member] = drinksMember(TeamRole::Member);

    expect($member->can('viewAny', Article::class))->toBeFalse()
        ->and($member->can('create', Article::class))->toBeFalse();
});

test('la policy refuse l\'accès à un article d\'une autre équipe', function () {
    [$admin] = drinksMember(TeamRole::Admin);
    $otherTeam = Team::factory()->create();
    $otherArticle = Article::factory()->create(['team_id' => $otherTeam->id]);

    expect($admin->can('view', $otherArticle))->toBeFalse()
        ->and($admin->can('update', $otherArticle))->toBeFalse()
        ->and($admin->can('delete', $otherArticle))->toBeFalse();
});
