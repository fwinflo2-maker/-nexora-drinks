<?php

declare(strict_types=1);

namespace App\Modules\FnB;

use Illuminate\Support\ServiceProvider;

class FnBServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(database_path('migrations/fnb'));
        $this->loadRoutesFrom(base_path('routes/fnb.php'));
    }
}
