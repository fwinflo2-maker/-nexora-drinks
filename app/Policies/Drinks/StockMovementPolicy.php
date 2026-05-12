<?php

declare(strict_types=1);

namespace App\Policies\Drinks;

use App\Enums\TeamPermission;
use App\Models\Drinks\StockMovement;
use App\Models\User;

class StockMovementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->currentTeam !== null
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksStockMovementsView);
    }

    public function view(User $user, StockMovement $movement): bool
    {
        return $movement->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksStockMovementsView);
    }
}
