<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drinks_stock_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->date('snapshot_date');
            $table->foreignId('article_id')->constrained('drinks_articles')->cascadeOnDelete();
            $table->decimal('cost_price', 12, 2)->nullable();
            $table->unsignedInteger('stock_qty')->default(0);
            $table->timestamps();

            $table->unique(['team_id', 'snapshot_date', 'article_id']);
            $table->index(['team_id', 'snapshot_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drinks_stock_snapshots');
    }
};
