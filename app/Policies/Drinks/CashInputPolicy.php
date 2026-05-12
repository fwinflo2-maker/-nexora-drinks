<?php

declare(strict_types=1);

namespace App\Policies\Drinks;

use App\Enums\TeamPermission;
use App\Models\Drinks\CashInput;
use App\Models\User;

class CashInputPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->currentTeam !== null
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksCashInputsView);
    }

    public function view(User $user, CashInput $cashInput): bool
    {
        return $cashInput->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksCashInputsView);
    }

    public function create(User $user): bool
    {
        return $user->currentTeam !== null
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksCashInputsCreate);
    }

    public function update(User $user, CashInput $cashInput): bool
    {
        return $cashInput->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksCashInputsUpdate);
    }

    public function validate(User $user, CashInput $cashInput): bool
    {
        return $cashInput->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksCashInputsValidate);
    }
}
