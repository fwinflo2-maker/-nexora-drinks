<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_folios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reservation_id')->constrained('hotel_reservations')->cascadeOnDelete();
            $table->string('label');
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['room', 'service', 'extra', 'discount']);
            $table->timestamps();

            $table->index(['team_id', 'reservation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_folios');
    }
};
