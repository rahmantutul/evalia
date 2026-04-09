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
        Schema::table('agent_evaluation_roles', function (Blueprint $table) {
            $table->boolean('eval_linguistic')->default(true)->after('eval_cooperation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agent_evaluation_roles', function (Blueprint $table) {
            $table->dropColumn('eval_linguistic');
        });
    }
};
