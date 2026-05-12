<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drinks_sale_packaging_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('drinks_sales')->cascadeOnDelete();
            $table->foreignId('packaging_id')->constrained('drinks_packagings')->restrictOnDelete();
            $table->decimal('quantity_out', 14, 3);
            $table->decimal('quantity_returned', 14, 3)->default(0);
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index('sale_id');
            $table->index(['packaging_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drinks_sale_packaging_lines');
    }
};
