<?php

namespace App\Providers;

use App\Events\DeliveryCompleted;
use App\Events\ExpenseCreated;
use App\Events\FnB\OrderClosed;
use App\Events\FnB\RoomServiceOrdered;
use App\Events\Hotel\ReservationCancelled;
use App\Events\Hotel\ReservationCheckedIn;
use App\Events\Hotel\ReservationCheckedOut;
use App\Events\OrderConfirmed;
use App\Events\PaymentReceived;
use App\Events\StockMovementCreated;
use App\Listeners\Automation\EvaluateAutomationRules;
use App\Listeners\FnB\CreateFolioIfLinkedToReservation;
use App\Listeners\FnB\NotifyReceptionDashboard;
use App\Listeners\Hotel\CloseAllLinkedFnBOrders;
use App\Listeners\Hotel\DetachLinkedFnBOrders;
use App\Listeners\Hotel\NotifyFnBModuleOfNewGuest;
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

        // ── Hotel ↔ F&B Bridge Events ─────────────────────────────────────────
        ReservationCheckedIn::class => [
            NotifyFnBModuleOfNewGuest::class,
        ],
        ReservationCheckedOut::class => [
            CloseAllLinkedFnBOrders::class,
        ],
        ReservationCancelled::class => [
            DetachLinkedFnBOrders::class,
        ],
        OrderClosed::class => [
            CreateFolioIfLinkedToReservation::class,
        ],
        RoomServiceOrdered::class => [
            NotifyReceptionDashboard::class,
        ],
    ];
}
