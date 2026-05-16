<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE hotel_folios MODIFY COLUMN type ENUM('room','service','extra','discount','restaurant','payment') NOT NULL");
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE hotel_folios MODIFY COLUMN type ENUM('room','service','extra','discount') NOT NULL");
        }
    }
};
