<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $user = $request->user();

        // Super-admin bypasses module check
        if ($user?->nexora_role === 'super_admin' || ($request->hasSession() && $request->session()->has('impersonator_id'))) {
            return $next($request);
        }

        $team = $user?->currentTeam;

        abort_if(! $team, 403, 'Aucune équipe active.');

        abort_unless(
            $team->hasModule($module),
            403,
            "Le module « {$module} » n'est pas activé pour ce compte."
        );

        return $next($request);
    }
}
