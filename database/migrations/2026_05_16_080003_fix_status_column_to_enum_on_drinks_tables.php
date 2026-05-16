<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const VALID_STATUSES = ['draft', 'validated', 'cancelled'];

    public function up(): void
    {
        // Normaliser les valeurs invalides avant la migration
        foreach (['drinks_sales', 'drinks_procurements'] as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)
                    ->whereNotIn('status', self::VALID_STATUSES)
                    ->update(['status' => 'draft']);

                Schema::table($table, function (Blueprint $blueprint) {
                    // MySQL: modifier la colonne en ENUM avec contrainte DB-level
                    $blueprint->enum('status', self::VALID_STATUSES)
                        ->default('draft')
                        ->change();
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['drinks_sales', 'drinks_procurements'] as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->string('status')->default('draft')->change();
                });
            }
        }
    }
};
