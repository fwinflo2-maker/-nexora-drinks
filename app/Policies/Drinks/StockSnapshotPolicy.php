<?php

declare(strict_types=1);

namespace App\Policies\Drinks;

use App\Enums\TeamPermission;
use App\Models\Drinks\StockSnapshot;
use App\Models\User;

class StockSnapshotPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->currentTeam !== null
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksStockSnapshotsView);
    }

    public function view(User $user, StockSnapshot $snapshot): bool
    {
        return $snapshot->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksStockSnapshotsView);
    }
}
