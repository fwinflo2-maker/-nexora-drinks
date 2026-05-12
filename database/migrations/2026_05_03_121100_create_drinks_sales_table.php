<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drinks_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('kind');
            $table->string('code');
            $table->date('document_date');
            $table->foreignId('client_id')->nullable()->constrained('drinks_clients')->nullOnDelete();
            $table->text('observation')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('validated_at')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('discount_total', 14, 2)->default(0);
            $table->decimal('rebate_credit', 14, 2)->default(0);
            $table->decimal('total_ht', 14, 2)->default(0);
            $table->decimal('total_ttc', 14, 2)->default(0);
            $table->timestamps();

            $table->unique(['team_id', 'code']);
            $table->index(['team_id', 'status']);
            $table->index(['team_id', 'document_date']);
            $table->index(['team_id', 'kind']);
            $table->index(['team_id', 'client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drinks_sales');
    }
};
