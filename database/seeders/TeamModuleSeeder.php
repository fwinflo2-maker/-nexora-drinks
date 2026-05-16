<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Module;
use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamModuleSeeder extends Seeder
{
    public function run(): void
    {
        Team::query()->each(function (Team $team): void {
            $team->activateModule(Module::Drinks->value);
        });

        $this->command->info('drinks activé pour '.Team::count().' team(s).');
    }
}
