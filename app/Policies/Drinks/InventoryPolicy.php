<?php

declare(strict_types=1);

namespace App\Policies\Drinks;

use App\Enums\TeamPermission;
use App\Models\Drinks\Inventory;
use App\Models\User;

class InventoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->currentTeam !== null
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksInventoriesView);
    }

    public function view(User $user, Inventory $inventory): bool
    {
        return $inventory->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksInventoriesView);
    }

    public function create(User $user): bool
    {
        return $user->currentTeam !== null
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksInventoriesCreate);
    }

    public function update(User $user, Inventory $inventory): bool
    {
        return $inventory->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksInventoriesUpdate);
    }

    public function delete(User $user, Inventory $inventory): bool
    {
        return $inventory->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksInventoriesDelete);
    }

    public function validate(User $user, Inventory $inventory): bool
    {
        return $inventory->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksInventoriesValidate);
    }
}
