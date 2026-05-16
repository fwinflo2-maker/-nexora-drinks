<?php

use App\Modules\FnB\FnBServiceProvider;
use App\Modules\Hotel\HotelServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\EventServiceProvider;
use App\Providers\FortifyServiceProvider;

return [
    AppServiceProvider::class,
    FortifyServiceProvider::class,
    EventServiceProvider::class,
    HotelServiceProvider::class,
    FnBServiceProvider::class,
];
