<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'teams',
            'products',
            'clients',
            'suppliers',
            'orders',
            'invoices',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && ! $this->hasIndex($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->index('deleted_at');
                });
            }
        }
    }

    public function down(): void
    {
        $tables = ['teams', 'products', 'clients', 'suppliers', 'orders', 'invoices'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropIndex(['deleted_at']);
                });
            }
        }
    }

    private function hasIndex(string $table, string $column): bool
    {
        $indexes = DB::select(
            "SHOW INDEX FROM `{$table}` WHERE Column_name = ?",
            [$column]
        );

        return count($indexes) > 0;
    }
};
