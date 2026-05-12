<?php

declare(strict_types=1);

namespace App\Policies\Drinks;

use App\Enums\TeamPermission;
use App\Models\Drinks\ExpenseType;
use App\Models\User;

class ExpenseTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->currentTeam !== null
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksExpenseTypesView);
    }

    public function view(User $user, ExpenseType $expenseType): bool
    {
        return $expenseType->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksExpenseTypesView);
    }

    public function create(User $user): bool
    {
        return $user->currentTeam !== null
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksExpenseTypesCreate);
    }

    public function update(User $user, ExpenseType $expenseType): bool
    {
        return $expenseType->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksExpenseTypesUpdate);
    }

    public function delete(User $user, ExpenseType $expenseType): bool
    {
        return $expenseType->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksExpenseTypesDelete);
    }
}
