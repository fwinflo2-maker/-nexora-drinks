<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Crée les tables pour la gestion des profils personnalisés par admin.
 * Architecture : Chaque admin peut créer des profils adaptés à son secteur métier
 * avec permissions granulaires (create, read, update, delete par module).
 */
return new class extends Migration
{
    public function up(): void
    {
        // Profils personnalisés par tenant (admin créé)
        Schema::create('custom_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);               // ex: "Chef de Zone", "Gestionnaire Stock"
            $table->text('description')->nullable();
            $table->json('permissions');               // {"stock": ["create","read","update"], "orders": ["read"]}
            $table->string('sector', 50)->nullable();  // drinks, food pour adapter l'UI
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['team_id', 'name']);
            $table->index(['team_id', 'is_active']);
        });

        // Agents IA assignés par dashboard/rôle
        Schema::create('dashboard_agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('role');                    // admin, comptable, magasinier, commercial, livreur
            $table->string('agent_name', 100);         // ex: "Agent Stock", "Agent Finance"
            $table->text('system_prompt');              // Instructions IA pour ce rôle
            $table->json('capabilities');               // ["stock_analysis", "report_generation", "alerts"]
            $table->json('config')->nullable();         // {"model": "gpt-4", "temperature": 0.7}
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['team_id', 'role']);
            $table->index(['team_id', 'is_active']);
        });

        // Conversations avec agents IA
        Schema::create('agent_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agent_id')->constrained('dashboard_agents')->cascadeOnDelete();
            $table->string('title')->nullable();        // Titre de la session
            $table->json('context')->nullable();        // Contexte métier (client_id, periode, etc)
            $table->unsignedInteger('message_count')->default(0);
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->index(['team_id', 'user_id']);
            $table->index(['user_id', 'created_at']);
        });

        // Messages dans conversations IA
        Schema::create('agent_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('agent_conversations')->cascadeOnDelete();
            $table->enum('sender', ['user', 'agent']);
            $table->longText('content');
            $table->json('metadata')->nullable();       // tokens_used, model_version, etc
            $table->json('attachments')->nullable();    // URLs fichiers uploadés
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
        });

        // GODMODE : audit trail des super-admin actions
        Schema::create('godmode_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('super_admin_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('target_team_id')->constrained('teams')->cascadeOnDelete();
            $table->string('action');                   // impersonate, toggle_maintenance, execute_query, etc
            $table->string('entity_type')->nullable();  // User, Team, Order, etc
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('changes')->nullable();        // {old: {...}, new: {...}}
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['super_admin_id', 'created_at']);
            $table->index(['target_team_id', 'created_at']);
            $table->index(['action']);
        });

        // GODMODE : system logs des opérations sensibles
        Schema::create('godmode_system_logs', function (Blueprint $table) {
            $table->id();
            $table->string('level');                    // info, warning, error, critical
            $table->string('type');                     // database_query, file_operation, system_call, etc
            $table->longText('message');
            $table->json('context')->nullable();        // params, result, error details
            $table->string('triggered_by')->nullable(); // super_admin_id ou system
            $table->timestamps();

            $table->index(['level', 'created_at']);
            $table->index(['type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('godmode_system_logs');
        Schema::dropIfExists('godmode_audit_logs');
        Schema::dropIfExists('agent_messages');
        Schema::dropIfExists('agent_conversations');
        Schema::dropIfExists('dashboard_agents');
        Schema::dropIfExists('custom_profiles');
    }
};
