<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Module Automatisation
 * - automation_rules : règles conditionnelles déclenchées par des événements métier
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('description', 500)->nullable();
            $table->string('trigger_event', 100);
            $table->string('condition_field', 100);
            $table->string('condition_operator', 10);
            $table->string('condition_value', 255);
            $table->string('action_type', 50);
            $table->json('action_params')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->integer('priority')->default(100);
            $table->tinyInteger('is_system')->default(0);
            $table->timestamps();

            $table->index(['team_id', 'trigger_event', 'is_active']);
            $table->index(['team_id', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_rules');
    }
};
