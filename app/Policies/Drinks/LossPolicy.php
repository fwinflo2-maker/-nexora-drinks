<?php

declare(strict_types=1);

namespace App\Policies\Drinks;

use App\Enums\TeamPermission;
use App\Models\Drinks\Loss;
use App\Models\User;

class LossPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->currentTeam !== null
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksLossesView);
    }

    public function view(User $user, Loss $loss): bool
    {
        return $loss->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksLossesView);
    }

    public function create(User $user): bool
    {
        return $user->currentTeam !== null
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksLossesCreate);
    }

    public function update(User $user, Loss $loss): bool
    {
        return $loss->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksLossesUpdate);
    }

    public function delete(User $user, Loss $loss): bool
    {
        return $loss->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksLossesDelete);
    }

    public function validate(User $user, Loss $loss): bool
    {
        return $loss->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksLossesValidate);
    }
}
