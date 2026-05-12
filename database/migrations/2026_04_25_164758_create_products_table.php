<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('sku', 50);
            $table->string('barcode', 50)->nullable();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('base_unit', 20)->default('bouteille');
            $table->unsignedSmallInteger('units_per_pack')->default(1);
            $table->unsignedSmallInteger('units_per_case')->default(1);
            $table->unsignedSmallInteger('units_per_pallet')->default(1);
            $table->decimal('purchase_price', 15, 2)->default(0);
            $table->decimal('sale_price', 15, 2)->default(0);
            $table->decimal('min_sale_price', 15, 2)->nullable();
            $table->decimal('vat_rate', 5, 2)->default(0);
            $table->boolean('is_consignable')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['team_id', 'sku']);
            $table->index(['team_id', 'is_active']);
            $table->index(['team_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
