<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Fortify;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): Response
    {
        $user = $request->user();

        // ── Super Admin : redirection dédiée (to() pour ignorer l'URL intended) ──
        if ($user?->nexora_role === 'super_admin') {
            return $request->wantsJson()
                ? new JsonResponse(['two_factor' => false], 200)
                : redirect()->to('/super-admin/dashboard');
        }

        // ── Utilisateurs normaux : redirection vers leur équipe ──────────
        // Utilise teams()->first() pour garantir un chargement fiable depuis la DB
        $team = $user?->teams()->orderByPivot('created_at')->first();

        if (! $team) {
            // Pas d'équipe trouvée → redirection sûre vers l'accueil
            return $request->wantsJson()
                ? new JsonResponse(['two_factor' => false], 200)
                : redirect()->route('home');
        }

        if (! $team->is_active) {
            return $request->wantsJson()
                ? new JsonResponse(['two_factor' => false], 200)
                : redirect()->route('pending-approval');
        }

        URL::defaults(['current_team' => $team->slug]);

        return $request->wantsJson()
            ? new JsonResponse(['two_factor' => false], 200)
            : redirect()->intended("/{$team->slug}".Fortify::redirects('login'));
    }
}
