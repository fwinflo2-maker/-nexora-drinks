<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\TeamPermission;
use App\Enums\TeamRole;
use Illuminate\Database\Seeder;

class FnBPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $fnbPermissions = collect(TeamPermission::cases())
            ->filter(fn (TeamPermission $p) => str_starts_with($p->value, 'fnb.'))
            ->values();

        $this->command->info("F&B permissions registered: {$fnbPermissions->count()}");

        $fnbRoles = [
            TeamRole::FnBManager,
            TeamRole::FnBWaiter,
            TeamRole::FnBKitchen,
            TeamRole::FnBCashier,
        ];

        foreach ($fnbRoles as $role) {
            $permCount = count($role->permissions());
            $this->command->info("Role [{$role->label()}] has {$permCount} permissions.");
        }

        $adminFnbPerms = collect(TeamRole::Admin->permissions())
            ->filter(fn (TeamPermission $p) => str_starts_with($p->value, 'fnb.'))
            ->count();

        $this->command->info("Admin role has {$adminFnbPerms} F&B permissions.");
    }
}
