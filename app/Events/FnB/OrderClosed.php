<?php

declare(strict_types=1);

namespace App\Events\FnB;

use App\Models\FnB\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderClosed
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Order $order) {}
}
