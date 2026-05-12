<?php

declare(strict_types=1);

namespace App\Policies\Drinks;

use App\Enums\TeamPermission;
use App\Models\Drinks\Packaging;
use App\Models\User;

class PackagingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->currentTeam !== null
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksPackagingsView);
    }

    public function view(User $user, Packaging $packaging): bool
    {
        return $packaging->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksPackagingsView);
    }

    public function create(User $user): bool
    {
        return $user->currentTeam !== null
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksPackagingsCreate);
    }

    public function update(User $user, Packaging $packaging): bool
    {
        return $packaging->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksPackagingsUpdate);
    }

    public function delete(User $user, Packaging $packaging): bool
    {
        return $packaging->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksPackagingsDelete);
    }
}
