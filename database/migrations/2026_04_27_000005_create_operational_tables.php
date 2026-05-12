<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tables transversales : visites terrain, logs de sync offline, notifications.
 * Essentielles pour le mode terrain offline des commerciaux.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Visites clients (CRM terrain) ──────────────────────────────────────
        Schema::create('client_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('commercial_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->timestamp('visited_at');
            $table->decimal('gps_lat', 10, 7)->nullable();
            $table->decimal('gps_lng', 10, 7)->nullable();
            $table->text('notes')->nullable();
            $table->enum('outcome', ['order', 'no_order', 'closed', 'absent'])->default('order');
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->timestamps();

            $table->index(['team_id', 'commercial_id']);
            $table->index(['team_id', 'client_id']);
            $table->index(['team_id', 'visited_at']);
        });

        // ── Journal de synchronisation offline ────────────────────────────────
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('device_id', 100)->nullable();
            $table->enum('sync_type', ['full', 'incremental', 'push'])->default('incremental');
            $table->enum('status', ['success', 'partial', 'error'])->default('success');
            $table->unsignedInteger('records_sent')->default(0);
            $table->unsignedInteger('records_received')->default(0);
            $table->json('errors_json')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'started_at']);
        });

        // ── Notifications in-app ──────────────────────────────────────────────
        Schema::create('nexora_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 100); // stock_alert, debt_threshold, delivery_complete...
            $table->string('title', 255);
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->json('data_json')->nullable(); // données contextuelles
            $table->timestamps();

            $table->index(['team_id', 'user_id', 'read_at']);
            $table->index(['team_id', 'type']);
        });

        // ── Réceptions fournisseurs ────────────────────────────────────────────
        Schema::create('receptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->string('reference', 50)->nullable();
            $table->enum('status', ['pending', 'partial', 'complete'])->default('pending');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->timestamp('received_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['team_id', 'status']);
            $table->index(['team_id', 'supplier_id']);
        });

        Schema::create('reception_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reception_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_cost', 15, 2);
            $table->string('lot_number', 50)->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reception_items');
        Schema::dropIfExists('receptions');
        Schema::dropIfExists('nexora_notifications');
        Schema::dropIfExists('sync_logs');
        Schema::dropIfExists('client_visits');
    }
};
