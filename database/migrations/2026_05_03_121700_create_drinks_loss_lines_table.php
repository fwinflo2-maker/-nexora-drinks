<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drinks_loss_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loss_id')->constrained('drinks_losses')->cascadeOnDelete();
            $table->foreignId('article_id')->constrained('drinks_articles')->restrictOnDelete();
            $table->decimal('quantity', 14, 3);
            $table->decimal('cost_price', 12, 2)->nullable();
            $table->timestamps();

            $table->index('loss_id');
            $table->index('article_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drinks_loss_lines');
    }
};
