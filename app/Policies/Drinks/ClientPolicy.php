<?php

declare(strict_types=1);

namespace App\Policies\Drinks;

use App\Enums\TeamPermission;
use App\Models\Drinks\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->currentTeam !== null
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksClientsView);
    }

    public function view(User $user, Client $client): bool
    {
        return $client->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksClientsView);
    }

    public function create(User $user): bool
    {
        return $user->currentTeam !== null
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksClientsCreate);
    }

    public function update(User $user, Client $client): bool
    {
        return $client->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksClientsUpdate);
    }

    public function delete(User $user, Client $client): bool
    {
        return $client->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::DrinksClientsDelete);
    }
}
