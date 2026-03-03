<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('group_id')->nullable();
            
            // Textual Information
            $table->text('company_overview')->nullable();
            $table->text('operating_hours')->nullable();
            $table->text('holidays')->nullable();
            
            // Delay and pause settings
            $table->float('delay_accept_limit')->default(0);
            $table->float('pause_accept_limit')->default(0);
            $table->float('delay_classes_medium')->default(2.4);
            $table->float('delay_classes_short')->default(1.2);
            $table->float('pause_classes_medium')->default(2.4);
            $table->float('pause_classes_short')->default(1.2);
            
            // Threshold settings
            $table->float('loudness_threshold')->default(0);
            $table->float('interactive_threshold')->default(0);
            $table->integer('common_words_threshold')->default(0);
            
            // API limits
            $table->integer('llm_api_limit')->default(100);
            $table->float('llm_total_usage_price')->default(0);
            $table->integer('transcription_api_limit')->default(100);
            $table->float('transcription_api_rate')->default(0.025);
            $table->integer('transcription_api_total_usage')->default(0);
            
            // Text prompts
            $table->text('qna_pair_prompt')->nullable();
            $table->text('gem_qna_pair_eval')->nullable();
            $table->text('gpt_qna_pair_eval')->nullable();
            $table->text('spelling_correction_prompt')->nullable();
            
            // Array/JSON fields
            $table->json('filler_words')->nullable();
            $table->json('main_topics')->nullable();
            $table->json('call_types')->nullable();
            $table->json('call_outcomes')->nullable();
            $table->json('restricted_phrases')->nullable();
            $table->json('source')->nullable();
            $table->json('company_policies')->nullable();
            $table->json('agent_assessments_configs')->nullable();
            $table->json('agent_cooperation_configs')->nullable();
            $table->json('agent_performance_configs')->nullable();
            $table->json('llm_total_usage')->nullable();
            $table->json('faq')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('companies');
    }
};