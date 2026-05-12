<?php

namespace App\Providers;

use App\Events\DeliveryCompleted;
use App\Events\ExpenseCreated;
use App\Events\OrderConfirmed;
use App\Events\PaymentReceived;
use App\Events\StockMovementCreated;
use App\Listeners\Automation\EvaluateAutomationRules;
use App\Listeners\Journal\RecordExpenseEntry;
use App\Listeners\Journal\RecordOrderSaleEntry;
use App\Listeners\Journal\RecordPaymentEntry;
use App\Listeners\Journal\RecordStockMovementEntry;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /** @var array<class-string, array<int, class-string>> */
    protected $listen = [
        OrderConfirmed::class => [
            RecordOrderSaleEntry::class,
            EvaluateAutomationRules::class,
        ],
        DeliveryCompleted::class => [
            EvaluateAutomationRules::class,
        ],
        PaymentReceived::class => [
            RecordPaymentEntry::class,
            EvaluateAutomationRules::class,
        ],
        StockMovementCreated::class => [
            RecordStockMovementEntry::class,
            EvaluateAutomationRules::class,
        ],
        ExpenseCreated::class => [
            RecordExpenseEntry::class,
        ],
    ];
}
