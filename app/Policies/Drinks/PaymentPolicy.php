<?php

declare(strict_types=1);

namespace App\Policies\Drinks;

use App\Enums\TeamPermission;
use App\Models\Drinks\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->currentTeam !== null
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksPaymentsView);
    }

    public function view(User $user, Payment $payment): bool
    {
        return $payment->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksPaymentsView);
    }

    public function create(User $user): bool
    {
        return $user->currentTeam !== null
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksPaymentsCreate);
    }

    public function update(User $user, Payment $payment): bool
    {
        return $payment->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksPaymentsUpdate);
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $payment->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksPaymentsDelete);
    }
}
