<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fnb_orders', function (Blueprint $table) {
            $table->foreignId('reservation_id')
                ->nullable()
                ->after('table_id')
                ->constrained('hotel_reservations')
                ->nullOnDelete();

            $table->enum('order_type', ['table', 'room_service', 'takeaway'])
                ->default('table')
                ->after('reservation_id');

            $table->index(['team_id', 'reservation_id']);
        });
    }

    public function down(): void
    {
        Schema::table('fnb_orders', function (Blueprint $table) {
            $table->dropForeign(['reservation_id']);
            $table->dropIndex(['team_id', 'reservation_id']);
            $table->dropColumn(['reservation_id', 'order_type']);
        });
    }
};
