<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Module Journal & Grand Livre (OHADA)
 * - business_journal_entries : écritures comptables sources
 * - ledger_accounts           : plan comptable OHADA par équipe
 * - ledger_lines              : lignes débit/crédit du grand livre
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Écritures journal ─────────────────────────────────────────────────
        Schema::create('business_journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('entry_type', 30);
            $table->decimal('amount', 15, 2);
            $table->string('reference_number', 50)->nullable();
            $table->text('description')->nullable();
            $table->timestamp('occurred_at');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->nullableMorphs('sourceable');
            $table->timestamps();

            $table->index(['team_id', 'entry_type']);
            $table->index(['team_id', 'occurred_at']);
            $table->index(['team_id', 'reference_number']);
        });

        // ── Plan comptable OHADA ──────────────────────────────────────────────
        Schema::create('ledger_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('code', 10);
            $table->string('name', 150);
            $table->string('account_type', 20);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('ledger_accounts')->nullOnDelete();
            $table->tinyInteger('is_active')->default(1);
            $table->tinyInteger('is_system')->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['team_id', 'code']);
            $table->index(['team_id', 'account_type']);
            $table->index(['team_id', 'is_active']);
        });

        // ── Lignes du grand livre ─────────────────────────────────────────────
        Schema::create('ledger_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('journal_entry_id')->constrained('business_journal_entries')->cascadeOnDelete();
            $table->foreignId('ledger_account_id')->constrained('ledger_accounts')->cascadeOnDelete();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamp('posted_at');
            $table->timestamps();

            $table->index(['team_id', 'ledger_account_id']);
            $table->index(['team_id', 'posted_at']);
            $table->index('journal_entry_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledger_lines');
        Schema::dropIfExists('ledger_accounts');
        Schema::dropIfExists('business_journal_entries');
    }
};
