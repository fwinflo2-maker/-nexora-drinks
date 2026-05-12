<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drinks_procurement_packaging_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procurement_id')->constrained('drinks_procurements')->cascadeOnDelete();
            $table->foreignId('packaging_id')->constrained('drinks_packagings')->restrictOnDelete();
            $table->decimal('quantity', 14, 3);
            $table->timestamps();

            $table->index('procurement_id');
            $table->index('packaging_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drinks_procurement_packaging_lines');
    }
};
