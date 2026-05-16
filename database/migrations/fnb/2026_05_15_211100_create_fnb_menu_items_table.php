<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fnb_menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('fnb_categories')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->string('sku')->nullable();
            $table->boolean('is_available')->default(true);
            $table->string('image_path')->nullable();
            $table->timestamps();

            $table->index('team_id');
            $table->index(['team_id', 'is_available']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fnb_menu_items');
    }
};
