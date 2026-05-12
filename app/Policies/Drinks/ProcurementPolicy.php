<?php

declare(strict_types=1);

namespace App\Policies\Drinks;

use App\Enums\TeamPermission;
use App\Models\Drinks\Procurement;
use App\Models\User;

class ProcurementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->currentTeam !== null
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksProcurementsView);
    }

    public function view(User $user, Procurement $procurement): bool
    {
        return $procurement->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksProcurementsView);
    }

    public function create(User $user): bool
    {
        return $user->currentTeam !== null
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksProcurementsCreate);
    }

    public function update(User $user, Procurement $procurement): bool
    {
        return $procurement->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksProcurementsUpdate);
    }

    public function delete(User $user, Procurement $procurement): bool
    {
        return $procurement->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksProcurementsDelete);
    }

    public function validate(User $user, Procurement $procurement): bool
    {
        return $procurement->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksProcurementsValidate);
    }
}
