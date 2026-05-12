<?php

namespace App\Http\Controllers;

use App\Enums\TeamRole;
use App\Models\Product;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function store(Request $request, Team $current_team): RedirectResponse
    {
        abort_unless(auth()->user()->teamRole($current_team)?->isAtLeast(TeamRole::Admin), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')->where('team_id', $current_team->id)],
            'sku' => ['nullable', 'string', 'max:100'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', Rule::in(['XAF', 'EUR', 'USD', 'GBP', 'NGN', 'CDF', 'GNF', 'XOF'])],
            'is_consignable' => ['boolean'],
        ]);

        $sku = $validated['sku'] ?? strtoupper('NXR-'.substr(md5($validated['name'].uniqid()), 0, 8));

        Product::create([
            'team_id' => $current_team->id,
            'name' => $validated['name'],
            'category_id' => $validated['category_id'] ?? null,
            'sku' => $sku,
            'purchase_price' => $validated['purchase_price'],
            'sale_price' => $validated['sale_price'],
            'currency' => $validated['currency'],
            'is_consignable' => $validated['is_consignable'] ?? false,
            'is_active' => true,
        ]);

        return back()->with('success', 'Produit créé.');
    }

    public function update(Request $request, Team $current_team, Product $product): RedirectResponse
    {
        abort_unless(auth()->user()->teamRole($current_team)?->isAtLeast(TeamRole::Admin), 403);
        abort_if($product->team_id !== $current_team->id, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')->where('team_id', $current_team->id)],
            'sku' => ['nullable', 'string', 'max:100'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', Rule::in(['XAF', 'EUR', 'USD', 'GBP', 'NGN', 'CDF', 'GNF', 'XOF'])],
            'is_consignable' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        $product->update($validated);

        return back()->with('success', 'Produit mis à jour.');
    }

    public function destroy(Team $current_team, Product $product): RedirectResponse
    {
        abort_unless(auth()->user()->teamRole($current_team)?->isAtLeast(TeamRole::Admin), 403);
        abort_if($product->team_id !== $current_team->id, 403);

        $product->delete();

        return back()->with('success', 'Produit supprimé.');
    }
}
