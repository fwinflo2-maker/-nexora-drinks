<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\TeamPermission;
use App\Models\CustomProfile;
use App\Models\User;

class CustomProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->currentTeam !== null
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::SettingsView);
    }

    public function view(User $user, CustomProfile $profile): bool
    {
        return $profile->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::SettingsView);
    }

    public function create(User $user): bool
    {
        return $user->currentTeam !== null
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::SettingsUpdate);
    }

    public function update(User $user, CustomProfile $profile): bool
    {
        return $profile->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::SettingsUpdate);
    }

    public function delete(User $user, CustomProfile $profile): bool
    {
        return $profile->team_id === $user->currentTeam?->id
            && $user->hasTeamPermission($user->currentTeam, TeamPermission::SettingsUpdate);
    }
}
