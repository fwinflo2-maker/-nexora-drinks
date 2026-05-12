<?php

namespace App\Http\Middleware;

use App\Models\Team;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('ResolveTenant@handle', ['url' => $request->fullUrl()]);
        $user = $request->user();

        // Pas besoin de résoudre un tenant pour le Super Admin ou les routes publiques
        if (! $user || $user->nexora_role === 'super_admin' || $this->shouldSkip($request)) {
            return $next($request);
        }

        $tenantIdentifier = $request->header('X-Tenant-ID') ?: $this->tenantFromSubdomain($request);

        if (! $tenantIdentifier) {
            return $next($request);
        }

        $team = Team::query()
            ->where('id', $tenantIdentifier)
            ->orWhere('slug', $tenantIdentifier)
            ->first();

        abort_if(! $team || ! $user->belongsToTeam($team), 403, 'Unauthorized tenant access.');

        if (! $user->isCurrentTeam($team)) {
            $user->switchTeam($team);
        }

        return $next($request);
    }

    /**
     * Routes à exclure de la résolution de tenant.
     */
    protected function shouldSkip(Request $request): bool
    {
        $path = ltrim($request->path(), '/');

        // Super admin, auth pages et routes systèmes
        return str_starts_with($path, 'super-admin')
            || str_starts_with($path, 'login')
            || str_starts_with($path, 'logout')
            || str_starts_with($path, 'register')
            || str_starts_with($path, 'forgot-password')
            || str_starts_with($path, 'reset-password')
            || str_starts_with($path, 'pending-approval')
            || str_starts_with($path, 'onboarding')
            || str_starts_with($path, '_')          // assets Vite
            || str_starts_with($path, 'up');        // health check
    }

    /**
     * Resolve tenant identifier from the first host segment.
     */
    protected function tenantFromSubdomain(Request $request): ?string
    {
        $host = $request->getHost();

        if (! $host || filter_var($host, FILTER_VALIDATE_IP)) {
            return null;
        }

        $segments = explode('.', $host);

        if (count($segments) < 3) {
            return null;
        }

        return $segments[0] ?: null;
    }
}
