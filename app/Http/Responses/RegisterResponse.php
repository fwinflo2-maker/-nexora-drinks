<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Laravel\Fortify\Fortify;
use Symfony\Component\HttpFoundation\Response;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request): Response
    {
        $user = $request->user();
        $team = $user?->currentTeam ?? $user?->personalTeam();

        if (! $team) {
            abort(403);
        }

        if (! $team->is_active) {
            return $request->wantsJson()
                ? new JsonResponse(['two_factor' => false], 201)
                : redirect()->route('pending-approval');
        }

        URL::defaults(['current_team' => $team->slug]);

        $sector = $team->sector ?? '';
        $redirectPath = match ($sector) {
            'hotel_fnb' => "/{$team->slug}/dashboard/hotel-fnb",
            'hotel' => "/{$team->slug}/hotel/dashboard",
            'fnb' => "/{$team->slug}/fnb/dashboard",
            default => "/{$team->slug}".Fortify::redirects('register'),
        };

        return $request->wantsJson()
            ? new JsonResponse(['two_factor' => false], 201)
            : redirect()->intended($redirectPath);
    }
}
