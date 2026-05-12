<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Crée les tables du module Consignation.
 * Spécificité distribution boissons africaine :
 *  - packaging_types    : types d'emballages consignés (casier 75cl, casier 33cl, etc.)
 *  - packaging_movements: historique des sorties/retours
 *  - client_packaging_balances: solde en temps réel par client/type
 *  - packaging_damages  : casses documentées
 */
return new class extends Migration
{
    public function up(): void
    {
        // Types d'emballages consignés
        Schema::create('packaging_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);               // ex: "Casier 75cl", "Casier 33cl", "Bouteille verre 1L"
            $table->string('description', 255)->nullable();
            $table->decimal('unit_value_xaf', 15, 2)->default(0); // valeur consigne en XAF
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['team_id', 'is_active']);
        });

        // Mouvements de consignes (out = livraison, in = retour)
        Schema::create('packaging_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('packaging_type_id')->constrained('packaging_types')->cascadeOnDelete();
            $table->enum('movement_type', ['out', 'in']); // out=livré, in=retourné
            $table->unsignedInteger('quantity');
            $table->foreignId('delivery_id')->nullable()->constrained()->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['team_id', 'client_id']);
            $table->index(['team_id', 'movement_type']);
            $table->index(['team_id', 'created_at']);
        });

        // Solde consignes par client (dénormalisé pour perf)
        Schema::create('client_packaging_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('packaging_type_id')->constrained('packaging_types')->cascadeOnDelete();
            $table->unsignedInteger('quantity_owed')->default(0); // consignes non retournées
            $table->timestamp('last_updated_at')->nullable();

            $table->unique(['client_id', 'packaging_type_id']);
            $table->index(['team_id', 'client_id']);
        });

        // Pertes et casses de consignes
        Schema::create('packaging_damages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('packaging_movement_id')->constrained('packaging_movements')->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->string('reason', 255)->nullable(); // bris, vol, etc.
            $table->decimal('cost_xaf', 15, 2)->default(0);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packaging_damages');
        Schema::dropIfExists('client_packaging_balances');
        Schema::dropIfExists('packaging_movements');
        Schema::dropIfExists('packaging_types');
    }
};
