<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drinks_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->date('document_date');
            $table->text('observation')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('validated_at')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['team_id', 'code']);
            $table->index(['team_id', 'status']);
            $table->index(['team_id', 'document_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drinks_inventories');
    }
};
