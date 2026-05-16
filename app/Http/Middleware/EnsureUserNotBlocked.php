<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserNotBlocked
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->isBlocked()) {
            auth()->logout();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Compte suspendu. Contactez votre administrateur.'], 403);
            }

            return redirect()->route('login')
                ->withErrors(['email' => 'Votre compte a été suspendu. Contactez votre administrateur.']);
        }

        return $next($request);
    }
}
