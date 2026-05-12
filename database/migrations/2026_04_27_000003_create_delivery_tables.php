<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Module Livraison & Tournées
 * Gestion des tournées de distribution journalières,
 * livraisons par client et suivi terrain.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Véhicules ─────────────────────────────────────────────────────────
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);          // "Camion 1", "Mercedes Benz 2"
            $table->string('plate', 30)->nullable(); // immatriculation
            $table->unsignedInteger('capacity_cases')->default(0); // capacité en casiers
            $table->boolean('is_active')->default(true);
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['team_id', 'is_active']);
        });

        // ── Tournées ──────────────────────────────────────────────────────────
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);          // "Tournée Akwa - 26/04/2026"
            $table->date('date');
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('planned');
            $table->decimal('total_distance_km', 8, 2)->nullable();
            $table->time('departure_time')->nullable();
            $table->time('arrival_time')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['team_id', 'date']);
            $table->index(['team_id', 'status']);
            $table->index(['team_id', 'driver_id']);
        });

        // ── Livraisons (une par commande dans une tournée) ────────────────────
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('route_id')->constrained('routes')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['pending', 'delivered', 'partial', 'failed'])->default('pending');
            $table->unsignedSmallInteger('sequence_number')->default(0); // ordre dans la tournée
            $table->timestamp('delivered_at')->nullable();
            $table->string('signature_path')->nullable(); // base64 PNG stocké
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['team_id', 'route_id']);
            $table->index(['team_id', 'status']);
            $table->index(['team_id', 'client_id']);
        });

        // ── Lignes de livraison (produits effectivement livrés) ───────────────
        Schema::create('delivery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('ordered_qty');
            $table->unsignedInteger('delivered_qty')->default(0);
            $table->unsignedInteger('returned_qty')->default(0); // reliquat
            $table->string('reason_partial', 255)->nullable(); // motif livraison partielle
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_items');
        Schema::dropIfExists('deliveries');
        Schema::dropIfExists('routes');
        Schema::dropIfExists('vehicles');
    }
};
