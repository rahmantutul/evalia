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
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('agent_id')->constrained('users')->onDelete('cascade');
            $table->string('audio_path')->nullable();
            $table->text('transcription')->nullable();
            $table->json('analysis')->nullable();
            $table->integer('score')->default(0);
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->string('source')->default('api');
            $table->string('channel')->default('Call');
            $table->string('sentiment')->default('Neutral');
            $table->string('risk_flag')->default('No');
            $table->string('outcome')->nullable();
            $table->string('lang')->default('ar');
            $table->string('duration')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropForeign(['agent_id']);
            $table->dropColumn([
                'company_id', 'agent_id', 'audio_path', 'transcription', 
                'analysis', 'score', 'status', 'source', 'channel', 
                'sentiment', 'risk_flag', 'outcome', 'lang', 'duration'
            ]);
        });
    }
};
