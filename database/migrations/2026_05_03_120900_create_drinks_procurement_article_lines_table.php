<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drinks_procurement_article_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procurement_id')->constrained('drinks_procurements')->cascadeOnDelete();
            $table->foreignId('article_id')->constrained('drinks_articles')->restrictOnDelete();
            $table->decimal('quantity_received', 14, 3);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('amount', 14, 2);
            $table->timestamps();

            $table->index('procurement_id');
            $table->index('article_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drinks_procurement_article_lines');
    }
};
