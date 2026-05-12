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
        Schema::table('drinks_expenses', function (Blueprint $table) {
            $table->string('code')->after('id');
            $table->unique(['team_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::table('drinks_expenses', function (Blueprint $table) {
            $table->dropUnique(['team_id', 'code']);
            $table->dropColumn('code');
        });
    }
};
