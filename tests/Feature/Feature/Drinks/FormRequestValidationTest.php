<?php

use App\Http\Requests\Drinks\StoreArticleRequest;
use App\Models\Drinks\Article;
use App\Models\Drinks\Category;
use App\Models\User;

test('StoreArticleRequest échoue si les champs requis sont absents', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $request = new StoreArticleRequest;
    $request->setUserResolver(fn () => auth()->user());

    $validator = validator()->make([], $request->rules());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('name'))->toBeTrue()
        ->and($validator->errors()->has('sale_price'))->toBeTrue()
        ->and($validator->errors()->has('category_id'))->toBeTrue();
});

test('StoreArticleRequest accepte des données valides', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $this->actingAs($user);

    $category = Category::factory()->create(['team_id' => $team->id]);

    $request = new StoreArticleRequest;
    $request->setUserResolver(fn () => auth()->user());

    $validator = validator()->make([
        'code' => 'TEST-001',
        'name' => 'Coca-Cola 50cl',
        'category_id' => $category->id,
        'sale_price' => 500,
    ], $request->rules());

    expect($validator->fails())->toBeFalse();
});

test('StoreArticleRequest rejette un code article déjà utilisé dans la même équipe', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $this->actingAs($user);

    $category = Category::factory()->create(['team_id' => $team->id]);

    Article::factory()->create([
        'team_id' => $team->id,
        'code' => 'DUPE-001',
    ]);

    $request = new StoreArticleRequest;
    $request->setUserResolver(fn () => auth()->user());

    $validator = validator()->make([
        'code' => 'DUPE-001',
        'name' => 'Autre article',
        'category_id' => $category->id,
        'sale_price' => 500,
    ], $request->rules());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('code'))->toBeTrue();
});
