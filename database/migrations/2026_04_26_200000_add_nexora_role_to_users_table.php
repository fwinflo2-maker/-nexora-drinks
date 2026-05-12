<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nexora_role')->nullable()->default(null)->after('current_team_id');
            // Valeurs possibles: super_admin | admin | comptable | logisticien | commercial | magasinier
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('nexora_role');
        });
    }
};
