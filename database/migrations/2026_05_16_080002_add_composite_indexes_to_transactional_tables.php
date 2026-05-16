<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Tables transactionnelles nécessitant un index (team_id, created_at) pour les rapports. */
    private const TABLES = [
        'drinks_sales',
        'drinks_procurements',
        'drinks_payments',
        'drinks_stock_movements',
        'drinks_stock_snapshots',
        'drinks_inventories',
        'drinks_expenses',
        'drinks_cash_inputs',
        'drinks_cash_deposits',
        'drinks_losses',
        'fnb_orders',
        'hotel_reservations',
        'hotel_folios',
        'journal_entries',
    ];

    public function up(): void
    {
        foreach (self::TABLES as $table) {
            if (Schema::hasTable($table) && Schema::hasColumns($table, ['team_id', 'created_at'])) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->index(['team_id', 'created_at'], "idx_{$blueprint->getTable()}_team_created");
                });
            }
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $name = "idx_{$blueprint->getTable()}_team_created";
                    try {
                        $blueprint->dropIndex($name);
                    } catch (Exception) {
                        // ignore if index didn't exist
                    }
                });
            }
        }
    }
};
