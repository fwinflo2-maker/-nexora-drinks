<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drinks_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->foreignId('client_id')->constrained('drinks_clients')->restrictOnDelete();
            $table->decimal('amount', 14, 2);
            $table->date('document_date');
            $table->string('mode');
            $table->foreignId('sale_id')->nullable()->constrained('drinks_sales')->nullOnDelete();
            $table->string('status')->default('validated');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['team_id', 'code']);
            $table->index(['team_id', 'document_date']);
            $table->index(['team_id', 'client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drinks_payments');
    }
};
