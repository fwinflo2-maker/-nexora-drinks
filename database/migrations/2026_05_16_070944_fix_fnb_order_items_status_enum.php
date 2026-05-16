<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE fnb_order_items MODIFY COLUMN status ENUM('pending','sent','preparing','ready','served') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE fnb_order_items MODIFY COLUMN status ENUM('pending','preparing','served') NOT NULL DEFAULT 'pending'");
    }
};
