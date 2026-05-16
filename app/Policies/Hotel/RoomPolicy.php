<?php

declare(strict_types=1);

namespace App\Policies\Hotel;

use App\Enums\TeamPermission;
use App\Models\Hotel\Room;
use App\Models\User;

class RoomPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->currentTeam?->hasModule('hotel') === true
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::HotelRoomsView);
    }

    public function view(User $user, Room $room): bool
    {
        return $room->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::HotelRoomsView);
    }

    public function create(User $user): bool
    {
        return $user->currentTeam?->hasModule('hotel') === true
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::HotelRoomsCreate);
    }

    public function update(User $user, Room $room): bool
    {
        return $room->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::HotelRoomsEdit);
    }

    public function delete(User $user, Room $room): bool
    {
        return $room->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::HotelRoomsDelete);
    }
}
