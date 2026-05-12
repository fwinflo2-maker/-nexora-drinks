<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drinks_procurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('kind');
            $table->string('code');
            $table->date('document_date');
            $table->foreignId('supplier_id')->nullable()->constrained('drinks_suppliers')->nullOnDelete();
            $table->text('observation')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('validated_at')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('total_ht', 14, 2)->default(0);
            $table->unsignedInteger('packs_count')->default(0);
            $table->timestamps();

            $table->unique(['team_id', 'code']);
            $table->index(['team_id', 'status']);
            $table->index(['team_id', 'document_date']);
            $table->index(['team_id', 'kind']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drinks_procurements');
    }
};
