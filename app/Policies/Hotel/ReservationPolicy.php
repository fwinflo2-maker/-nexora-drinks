<?php

declare(strict_types=1);

namespace App\Policies\Hotel;

use App\Enums\TeamPermission;
use App\Models\Hotel\Reservation;
use App\Models\User;

class ReservationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->currentTeam?->hasModule('hotel') === true
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::HotelReservationsView);
    }

    public function view(User $user, Reservation $reservation): bool
    {
        return $reservation->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::HotelReservationsView);
    }

    public function create(User $user): bool
    {
        return $user->currentTeam?->hasModule('hotel') === true
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::HotelReservationsCreate);
    }

    public function update(User $user, Reservation $reservation): bool
    {
        return $reservation->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::HotelReservationsEdit);
    }

    public function delete(User $user, Reservation $reservation): bool
    {
        return $reservation->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::HotelReservationsDelete);
    }

    public function checkIn(User $user, Reservation $reservation): bool
    {
        return $reservation->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::HotelReservationsCheckin);
    }

    public function checkOut(User $user, Reservation $reservation): bool
    {
        return $reservation->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::HotelReservationsCheckout);
    }

    public function cancel(User $user, Reservation $reservation): bool
    {
        return $reservation->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::HotelReservationsCancel);
    }
}
