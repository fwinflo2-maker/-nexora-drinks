<?php

namespace App\Http\Controllers\Api;

use App\Models\CustomProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomProfileController
{
    /**
     * Liste tous les profils du tenant
     */
    public function index(Request $request): JsonResponse
    {
        $team = $request->user()->currentTeam;
        $profiles = CustomProfile::where('team_id', $team->id)
            ->with(['creator', 'updater'])
            ->latest()
            ->paginate(25);

        return response()->json([
            'data' => $profiles->items(),
            'meta' => [
                'total' => $profiles->total(),
                'current_page' => $profiles->currentPage(),
                'per_page' => $profiles->perPage(),
            ],
        ]);
    }

    /**
     * Crée un nouvel profil personnalisé
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:custom_profiles,name,NULL,id,team_id,'.$request->user()->current_team_id,
            'description' => 'nullable|string|max:500',
            'sector' => 'nullable|in:drinks,food',
            'permissions' => 'required|array',
            'permissions.*' => 'array',
        ]);

        $team = $request->user()->currentTeam;

        $profile = CustomProfile::create([
            'team_id' => $team->id,
            'name' => $request->name,
            'description' => $request->description,
            'sector' => $request->sector,
            'permissions' => $request->permissions,
            'is_active' => true,
            'created_by' => $request->user()->id,
        ]);

        return response()->json([
            'data' => $profile->load(['creator']),
            'message' => 'Profil créé avec succès',
        ], 201);
    }

    /**
     * Affiche un profil spécifique
     */
    public function show(Request $request, CustomProfile $profile): JsonResponse
    {
        $this->authorize('view', $profile);

        return response()->json([
            'data' => $profile->load(['creator', 'updater', 'users']),
        ]);
    }

    /**
     * Met à jour un profil
     */
    public function update(Request $request, CustomProfile $profile): JsonResponse
    {
        $this->authorize('update', $profile);

        $request->validate([
            'name' => 'sometimes|string|max:100|unique:custom_profiles,name,'.$profile->id.',id,team_id,'.$profile->team_id,
            'description' => 'nullable|string|max:500',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'array',
            'is_active' => 'sometimes|boolean',
        ]);

        $profile->update([
            'name' => $request->name ?? $profile->name,
            'description' => $request->description ?? $profile->description,
            'permissions' => $request->permissions ?? $profile->permissions,
            'is_active' => $request->is_active ?? $profile->is_active,
            'updated_by' => $request->user()->id,
        ]);

        return response()->json([
            'data' => $profile->refresh()->load(['creator', 'updater']),
            'message' => 'Profil mis à jour avec succès',
        ]);
    }

    /**
     * Supprime un profil (soft delete)
     */
    public function destroy(Request $request, CustomProfile $profile): JsonResponse
    {
        $this->authorize('delete', $profile);

        // Vérifier qu'aucun utilisateur n'est assigné à ce profil
        if ($profile->users()->count() > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer : des utilisateurs sont assignés à ce profil.',
                'users_count' => $profile->users()->count(),
            ], 422);
        }

        $profile->update(['is_active' => false]);

        return response()->json([
            'message' => 'Profil archivé avec succès',
        ]);
    }

    /**
     * Duplique un profil existant
     */
    public function duplicate(Request $request, CustomProfile $profile): JsonResponse
    {
        $this->authorize('view', $profile);

        $newProfile = $profile->duplicate(
            $request->user()->currentTeam,
            $request->user()
        );

        return response()->json([
            'data' => $newProfile,
            'message' => 'Profil dupliqué avec succès',
        ], 201);
    }

    /**
     * Récupère les utilisateurs assignés à un profil
     */
    public function users(Request $request, CustomProfile $profile): JsonResponse
    {
        $this->authorize('view', $profile);

        $users = $profile->users()
            ->select(['id', 'name', 'email', 'nexora_role'])
            ->paginate(50);

        return response()->json([
            'data' => $users->items(),
            'meta' => [
                'total' => $users->total(),
                'current_page' => $users->currentPage(),
            ],
        ]);
    }
}
