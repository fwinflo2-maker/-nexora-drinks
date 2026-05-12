<?php

declare(strict_types=1);

namespace App\Http\Controllers\Drinks;

use App\Enums\TeamRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Drinks\UpdateSettingRequest;
use App\Models\Drinks\Setting;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function index(Request $request, Team $current_team): Response
    {
        Gate::authorize('viewAny', Setting::class);

        $settings = Setting::all();

        $members = $current_team->members()
            ->get()
            ->map(fn (User $u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->pivot->role instanceof TeamRole ? $u->pivot->role->value : (string) $u->pivot->role,
                'roleLabel' => ($u->pivot->role instanceof TeamRole ? $u->pivot->role : TeamRole::tryFrom((string) $u->pivot->role))?->label() ?? (string) $u->pivot->role,
                'is_owner' => ($u->pivot->role instanceof TeamRole ? $u->pivot->role : TeamRole::tryFrom((string) $u->pivot->role)) === TeamRole::Owner,
                'blocked_at' => $u->blocked_at?->toIso8601String(),
            ]);

        return Inertia::render('drinks/settings/index', [
            'settings' => $settings,
            'members' => $members,
        ]);
    }

    public function update(UpdateSettingRequest $request, Team $current_team, Setting $setting): RedirectResponse
    {
        Gate::authorize('update', $setting);

        $setting->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Paramètre mis à jour.')]);

        return redirect()->back();
    }

    public function updateBranding(Request $request, Team $current_team): RedirectResponse
    {
        // Only admins can update branding
        if (! $request->user()->isTeamAdmin($current_team)) {
            abort(403);
        }

        $validated = $request->validate([
            'logo' => ['nullable', 'image', 'max:2048'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'rccm' => ['nullable', 'string', 'max:50'],
        ]);

        if ($request->hasFile('logo')) {
            if ($current_team->logo_path) {
                Storage::disk('public')->delete($current_team->logo_path);
            }
            $current_team->logo_path = $request->file('logo')->store('logos', 'public');
        }

        $settings = $current_team->settings_json ?? [];
        $settings['phone'] = $validated['phone'];
        $settings['address'] = $validated['address'];
        $settings['rccm'] = $validated['rccm'];

        $current_team->settings_json = $settings;
        $current_team->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Identité visuelle mise à jour.')]);

        return redirect()->back();
    }
}
