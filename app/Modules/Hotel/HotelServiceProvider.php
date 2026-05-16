<?php

declare(strict_types=1);

namespace App\Modules\Hotel;

use Illuminate\Support\ServiceProvider;

class HotelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(database_path('migrations/hotel'));
        $this->loadRoutesFrom(base_path('routes/hotel.php'));
    }
}
