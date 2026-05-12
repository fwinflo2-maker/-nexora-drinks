<?php

declare(strict_types=1);

namespace App\Console\Commands\Drinks;

use App\Models\Team;
use App\Services\Drinks\SnapshotService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TakeDailyStockSnapshot extends Command
{
    /** @var string */
    protected $signature = 'drinks:snapshot {--date= : Date YYYY-MM-DD (défaut: aujourd\'hui)}';

    /** @var string */
    protected $description = 'Prend un snapshot du stock pour tous les articles actifs de chaque team active.';

    public function handle(SnapshotService $service): int
    {
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))
            : Carbon::today();

        $teams = Team::where('is_active', true)->get();

        $this->info("Démarrage du snapshot stock pour {$teams->count()} team(s) au {$date->toDateString()}.");

        $totalArticles = 0;

        foreach ($teams as $team) {
            $count = $service->take($team, $date);
            $totalArticles += $count;
            $this->line("  Team [{$team->id}] {$team->name} : {$count} article(s) snapshoté(s).");
        }

        $this->info("Snapshot terminé. Total : {$totalArticles} article(s) sur {$teams->count()} team(s).");

        return self::SUCCESS;
    }
}
