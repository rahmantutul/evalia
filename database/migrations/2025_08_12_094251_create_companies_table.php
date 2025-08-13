<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->string('company_id')->primary();
            $table->string('company_name');
            $table->string('group_id')->nullable();
            
            // Delay and pause settings
            $table->float('delay_accept_limit');
            $table->float('pause_accept_limit');
            $table->float('delay_medium');
            $table->float('delay_short');
            $table->float('pause_medium');
            $table->float('pause_short');
            
            // Threshold settings
            $table->float('loudness_threshold')->default(0);
            $table->float('interactive_threshold')->default(0);
            $table->integer('common_words_threshold');
            
            // API limits
            $table->integer('llm_api_limit');
            $table->float('llm_total_usage_price')->default(0);
            $table->integer('transcription_api_limit');
            $table->float('transcription_api_rate');
            $table->integer('transcription_api_total_usage')->default(0);
            
            // Text prompts
            $table->text('qna_pair_prompt')->nullable();
            $table->text('gem_qna_pair_eval')->nullable();
            $table->text('gpt_qna_pair_eval')->nullable();
            $table->text('spelling_correction_prompt')->nullable();
            
            // Array fields (stored as JSON)
            $table->json('filler_words')->nullable();
            $table->json('main_topics')->nullable();
            $table->json('call_types')->nullable();
            $table->json('call_outcomes')->nullable();
            $table->json('company_policies')->nullable();
            $table->json('agent_assessments_configs')->nullable();
            $table->json('agent_cooperation_configs')->nullable();
            $table->json('agent_performance_configs')->nullable();
            $table->json('llm_total_usage')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('companies');
    }
};