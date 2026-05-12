<?php

declare(strict_types=1);

namespace App\Policies\Drinks;

use App\Enums\TeamPermission;
use App\Models\Drinks\PricingTier;
use App\Models\User;

class PricingTierPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->currentTeam !== null
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksPricingTiersView);
    }

    public function view(User $user, PricingTier $pricingTier): bool
    {
        return $pricingTier->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksPricingTiersView);
    }

    public function create(User $user): bool
    {
        return $user->currentTeam !== null
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksPricingTiersCreate);
    }

    public function update(User $user, PricingTier $pricingTier): bool
    {
        return $pricingTier->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksPricingTiersUpdate);
    }

    public function delete(User $user, PricingTier $pricingTier): bool
    {
        return $pricingTier->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksPricingTiersDelete);
    }
}
