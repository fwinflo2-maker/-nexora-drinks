<?php

use App\Enums\TeamRole;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin peut créer un produit avec currency EUR', function () {
    $owner = User::factory()->create();
    $team = $owner->currentTeam;
    $category = Category::factory()->create(['team_id' => $team->id]);

    $this->actingAs($owner)
        ->post(route('produits.store', ['current_team' => $team->slug]), [
            'name' => 'Bière Castel',
            'purchase_price' => 800,
            'sale_price' => 1200,
            'currency' => 'EUR',
            'category_id' => $category->id,
        ])
        ->assertRedirect();

    expect(Product::where('team_id', $team->id)->where('name', 'Bière Castel')->first())
        ->not->toBeNull()
        ->currency->toBe('EUR');
});

test('admin peut modifier le prix et la devise', function () {
    $owner = User::factory()->create();
    $team = $owner->currentTeam;
    $product = Product::factory()->create(['team_id' => $team->id, 'currency' => 'XAF']);

    $this->actingAs($owner)
        ->patch(route('produits.update', ['current_team' => $team->slug, 'product' => $product->id]), [
            'name' => $product->name,
            'purchase_price' => 1000,
            'sale_price' => 1500,
            'currency' => 'USD',
        ])
        ->assertRedirect();

    expect($product->fresh()->currency)->toBe('USD');
    expect((float) $product->fresh()->sale_price)->toBe(1500.0);
});

test('admin peut supprimer un produit (soft delete)', function () {
    $owner = User::factory()->create();
    $team = $owner->currentTeam;
    $product = Product::factory()->create(['team_id' => $team->id]);

    $this->actingAs($owner)
        ->delete(route('produits.destroy', ['current_team' => $team->slug, 'product' => $product->id]))
        ->assertRedirect();

    expect(Product::withoutTrashed()->find($product->id))->toBeNull();
});

test('non-admin reçoit 403 sur store', function () {
    $owner = User::factory()->create();
    $team = $owner->currentTeam;
    $member = User::factory()->create();
    $team->members()->attach($member->id, ['role' => TeamRole::Member->value]);

    $this->actingAs($member)
        ->post(route('produits.store', ['current_team' => $team->slug]), [
            'name' => 'Test',
            'purchase_price' => 100,
            'sale_price' => 200,
            'currency' => 'XAF',
        ])
        ->assertForbidden();
});

test('produit d\'un autre team est invisible (404) depuis une autre team', function () {
    $owner1 = User::factory()->create();
    $team1 = $owner1->currentTeam;
    $owner2 = User::factory()->create();
    $team2 = $owner2->currentTeam;
    $product = Product::factory()->create(['team_id' => $team2->id]);

    // Le global scope BelongsToTeam filtre par current_team_id → 404 avant même le contrôleur
    $this->actingAs($owner1)
        ->patch(route('produits.update', ['current_team' => $team1->slug, 'product' => $product->id]), [
            'name' => 'Hack',
            'purchase_price' => 0,
            'sale_price' => 0,
            'currency' => 'XAF',
        ])
        ->assertNotFound();
});
