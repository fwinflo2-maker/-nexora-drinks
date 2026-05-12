<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drinks_sale_article_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('drinks_sales')->cascadeOnDelete();
            $table->foreignId('article_id')->constrained('drinks_articles')->restrictOnDelete();
            $table->decimal('quantity', 14, 3);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('amount_ht', 14, 2);
            $table->decimal('amount_ttc', 14, 2);
            $table->text('observation')->nullable();
            $table->timestamps();

            $table->index('sale_id');
            $table->index('article_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drinks_sale_article_lines');
    }
};
