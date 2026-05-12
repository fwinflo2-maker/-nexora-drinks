<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drinks_pricing_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('article_id')->constrained('drinks_articles')->cascadeOnDelete();
            $table->string('label');
            $table->decimal('unit_price', 12, 2);
            $table->timestamps();

            $table->index(['team_id', 'article_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drinks_pricing_tiers');
    }
};
