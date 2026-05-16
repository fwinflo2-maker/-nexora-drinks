<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE fnb_tables MODIFY COLUMN status ENUM('free','occupied','reserved','closed') NOT NULL DEFAULT 'free'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE fnb_tables MODIFY COLUMN status ENUM('free','occupied','reserved') NOT NULL DEFAULT 'free'");
    }
};
