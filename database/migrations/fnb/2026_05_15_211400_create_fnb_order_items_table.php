<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fnb_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('fnb_orders')->cascadeOnDelete();
            $table->foreignId('menu_item_id')->constrained('fnb_menu_items')->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->enum('status', ['pending', 'preparing', 'served'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fnb_order_items');
    }
};
