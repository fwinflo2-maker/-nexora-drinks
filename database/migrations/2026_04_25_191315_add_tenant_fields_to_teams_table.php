<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('plan', 30)->default('starter')->after('is_personal');
            $table->json('settings_json')->nullable()->after('plan');
            $table->string('logo_path')->nullable()->after('settings_json');
            $table->string('domain')->nullable()->unique()->after('logo_path');
            $table->boolean('is_active')->default(false)->after('domain');
            $table->timestamp('trial_ends_at')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn([
                'plan',
                'settings_json',
                'logo_path',
                'domain',
                'is_active',
                'trial_ends_at',
            ]);
        });
    }
};
