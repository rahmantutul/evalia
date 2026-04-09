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
        Schema::create('agent_evaluation_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            
            // Evaluation Criteria Flags
            $table->boolean('eval_kb')->default(true);
            $table->boolean('eval_policies')->default(true);
            $table->boolean('eval_risks')->default(true);
            $table->boolean('eval_extractions')->default(true);
            $table->boolean('eval_professionalism')->default(true);
            $table->boolean('eval_assessment')->default(true);
            $table->boolean('eval_cooperation')->default(true);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_evaluation_roles');
    }
};
