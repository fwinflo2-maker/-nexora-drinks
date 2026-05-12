<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drinks_payment_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sale_id')->constrained('drinks_sales')->cascadeOnDelete();
            $table->decimal('amount', 14, 2);
            $table->text('observation')->nullable();
            $table->string('status')->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['team_id', 'sale_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drinks_payment_adjustments');
    }
};
