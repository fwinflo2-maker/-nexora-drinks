<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\TeamPermission;
use App\Enums\TeamRole;
use Illuminate\Database\Seeder;

/**
 * Seeds hotel roles and permissions into the team_role_permissions concept.
 *
 * Since Nexora uses a role-based enum (not Spatie), this seeder is informational
 * and used to validate the enum configuration + seed default data if needed.
 */
class HotelPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Verify hotel permissions are present in the enum
        $hotelPermissions = collect(TeamPermission::cases())
            ->filter(fn (TeamPermission $p) => str_starts_with($p->value, 'hotel.'))
            ->values();

        $this->command->info("Hotel permissions registered: {$hotelPermissions->count()}");

        // Verify hotel roles exist
        $hotelRoles = [
            TeamRole::HotelManager,
            TeamRole::HotelReceptionist,
            TeamRole::HotelHousekeeper,
        ];

        foreach ($hotelRoles as $role) {
            $permCount = count($role->permissions());
            $this->command->info("Role [{$role->label()}] has {$permCount} permissions.");
        }

        // Verify Admin role includes hotel permissions
        $adminHotelPerms = collect(TeamRole::Admin->permissions())
            ->filter(fn (TeamPermission $p) => str_starts_with($p->value, 'hotel.'))
            ->count();

        $this->command->info("Admin role has {$adminHotelPerms} hotel permissions.");
    }
}
