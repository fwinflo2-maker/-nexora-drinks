<?php

declare(strict_types=1);

namespace App\Policies\Drinks;

use App\Enums\TeamPermission;
use App\Models\Drinks\CashDeposit;
use App\Models\User;

class CashDepositPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->currentTeam !== null
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksCashDepositsView);
    }

    public function view(User $user, CashDeposit $cashDeposit): bool
    {
        return $cashDeposit->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksCashDepositsView);
    }

    public function create(User $user): bool
    {
        return $user->currentTeam !== null
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksCashDepositsCreate);
    }

    public function update(User $user, CashDeposit $cashDeposit): bool
    {
        return $cashDeposit->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksCashDepositsUpdate);
    }

    public function validate(User $user, CashDeposit $cashDeposit): bool
    {
        return $cashDeposit->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksCashDepositsValidate);
    }
}
