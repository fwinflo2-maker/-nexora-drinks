<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fnb_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->foreignId('table_id')->nullable()->constrained('fnb_tables')->nullOnDelete();
            $table->foreignId('waiter_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['open', 'sent', 'preparing', 'ready', 'closed', 'cancelled'])->default('open');
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamp('validated_at')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('team_id');
            $table->index(['team_id', 'status']);
            $table->index(['team_id', 'table_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fnb_orders');
    }
};
