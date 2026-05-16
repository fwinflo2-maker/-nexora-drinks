<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fnb_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('capacity')->default(4);
            $table->string('location')->nullable();
            $table->enum('status', ['free', 'occupied', 'reserved'])->default('free');
            $table->timestamps();

            $table->index('team_id');
            $table->index(['team_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fnb_tables');
    }
};
