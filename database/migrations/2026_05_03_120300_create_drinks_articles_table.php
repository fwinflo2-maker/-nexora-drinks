<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drinks_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->string('brand')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('drinks_categories')->nullOnDelete();
            $table->foreignId('packaging_id')->nullable()->constrained('drinks_packagings')->nullOnDelete();
            $table->decimal('sale_price', 12, 2)->default(0);
            $table->decimal('retail_price', 12, 2)->default(0);
            $table->decimal('cost_price', 12, 2)->default(0);
            $table->decimal('stock_qty', 14, 3)->default(0);
            $table->decimal('frigo_stock_qty', 14, 3)->default(0);
            $table->unsignedInteger('packs_per_unit')->default(1);
            $table->decimal('discount_rate', 5, 2)->default(0);
            $table->decimal('rebate_rate', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_consignable')->default(false);
            $table->timestamps();

            $table->unique(['team_id', 'code']);
            $table->index(['team_id', 'category_id']);
            $table->index(['team_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drinks_articles');
    }
};
