<?php

declare(strict_types=1);

namespace App\Policies\FnB;

use App\Enums\TeamPermission;
use App\Models\FnB\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->currentTeam?->hasModule('fnb') === true
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::FnBOrdersView);
    }

    public function view(User $user, Order $order): bool
    {
        return $order->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::FnBOrdersView);
    }

    public function create(User $user): bool
    {
        return $user->currentTeam?->hasModule('fnb') === true
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::FnBOrdersCreate);
    }

    public function update(User $user, Order $order): bool
    {
        return $order->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::FnBOrdersEdit);
    }

    public function close(User $user, Order $order): bool
    {
        return $order->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::FnBOrdersClose);
    }

    public function cancel(User $user, Order $order): bool
    {
        return $order->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::FnBOrdersCancel);
    }
}
