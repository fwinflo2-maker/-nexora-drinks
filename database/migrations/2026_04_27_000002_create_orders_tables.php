<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Module Commandes — Cycle complet :
 * BROUILLON → CONFIRMÉE → EN PRÉPA → CHARGÉE → LIVRÉE → FACTURÉE
 *
 * Canaux : terrain (offline), télévente, import CSV
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Commandes ─────────────────────────────────────────────────────────
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('order_number', 30)->unique(); // NX-2026-00001
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->enum('channel', ['terrain', 'televente', 'client_direct', 'import'])->default('televente');
            $table->enum('status', [
                'draft', 'confirmed', 'preparing', 'loaded', 'delivered', 'invoiced', 'cancelled',
            ])->default('draft');
            $table->date('delivery_date')->nullable();
            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('commercial_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('synced_at')->nullable(); // sync depuis terrain
            $table->timestamps();
            $table->softDeletes();

            $table->index(['team_id', 'status']);
            $table->index(['team_id', 'client_id']);
            $table->index(['team_id', 'delivery_date']);
            $table->index(['team_id', 'commercial_id']);
        });

        // ── Lignes de commande ─────────────────────────────────────────────────
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('discount_pct', 5, 2)->default(0);
            $table->decimal('line_total', 15, 2);
            $table->timestamps();

            $table->index(['order_id']);
        });

        // ── Commandes terrain offline (avant sync serveur) ─────────────────────
        Schema::create('field_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('commercial_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->json('items_json');             // [{product_id, qty, price}, ...]
            $table->decimal('gps_lat', 10, 7)->nullable();
            $table->decimal('gps_lng', 10, 7)->nullable();
            $table->timestamp('offline_created_at'); // timestamp client
            $table->timestamp('synced_at')->nullable();
            $table->enum('sync_status', ['pending', 'synced', 'error'])->default('pending');
            $table->foreignId('converted_order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->text('sync_error')->nullable();
            $table->timestamps();

            $table->index(['team_id', 'sync_status']);
            $table->index(['commercial_id', 'sync_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_orders');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
