<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Module Finance & Facturation
 * - invoices : factures, proformas, avoirs
 * - payments : paiements multi-méthodes (Mobile Money inclus)
 * - cash_sessions : sessions de caisse POS
 * - expenses : dépenses opérationnelles
 * - client_prices : prix personnalisés par client
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Prix personnalisés par client ─────────────────────────────────────
        Schema::create('client_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 15, 2);
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['client_id', 'product_id']);
            $table->index(['team_id', 'client_id']);
        });

        // ── Factures ──────────────────────────────────────────────────────────
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number', 30)->unique(); // FAC-2026-00001
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['invoice', 'proforma', 'credit_note'])->default('invoice');
            $table->enum('status', ['draft', 'sent', 'paid', 'partial', 'overdue', 'cancelled'])->default('draft');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['team_id', 'status']);
            $table->index(['team_id', 'client_id']);
            $table->index(['team_id', 'due_date']);
            $table->index(['team_id', 'type']);
        });

        // ── Paiements (multi-méthodes africaines) ─────────────────────────────
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->enum('method', [
                'cash',           // Espèces
                'orange_money',   // Orange Money
                'mtn_momo',       // MTN Mobile Money
                'wave',           // Wave
                'cheque',         // Chèque
                'transfer',       // Virement bancaire
                'credit',         // Crédit client
            ])->default('cash');
            $table->string('reference', 100)->nullable();     // N° chèque, reçu, etc.
            $table->string('mobile_money_ref', 100)->nullable(); // Réf. transaction MM
            $table->timestamp('received_at');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['team_id', 'method']);
            $table->index(['team_id', 'client_id']);
            $table->index(['team_id', 'received_at']);
        });

        // ── Sessions de caisse POS ─────────────────────────────────────────────
        Schema::create('cash_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cashier_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('closing_balance', 15, 2)->nullable();
            $table->decimal('total_sales', 15, 2)->default(0);
            $table->decimal('total_cash', 15, 2)->default(0);
            $table->decimal('total_mobile', 15, 2)->default(0);
            $table->decimal('discrepancy', 15, 2)->nullable(); // écart théorique/réel
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['team_id', 'cashier_id']);
            $table->index(['team_id', 'opened_at']);
        });

        // ── Dépenses opérationnelles ───────────────────────────────────────────
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('category', 100); // carburant, entretien, salaires...
            $table->string('description', 255)->nullable();
            $table->decimal('amount', 15, 2);
            $table->enum('payment_method', ['cash', 'orange_money', 'mtn_momo', 'wave', 'cheque', 'transfer'])->default('cash');
            $table->string('receipt_path')->nullable();
            $table->date('date');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['team_id', 'category']);
            $table->index(['team_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('cash_sessions');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('client_prices');
    }
};
