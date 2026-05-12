<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Protège les routes réservées au Super Administrateur NEXORA.
 * Redirige les non-super-admin vers leur dashboard d'équipe ou la page d'accueil.
 */
class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->nexora_role !== 'super_admin') {
            \Illuminate\Support\Facades\Log::warning('SuperAdmin access denied', [
                'user_id' => $user?->id,
                'role' => $user?->nexora_role,
                'path' => $request->path()
            ]);
            abort(403, '[EnsureSuperAdmin] Accès réservé au Super Administrateur NEXORA.');
        }

        return $next($request);
    }
}
