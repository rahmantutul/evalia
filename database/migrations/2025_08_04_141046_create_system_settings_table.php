<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            
            // Company Information
            $table->string('company_name')->default('Your Company');
            $table->string('company_tagline')->nullable();
            $table->string('company_logo')->nullable();
            $table->string('favicon')->nullable();
            
            // Pricing Page Content
            $table->string('pricing_title')->default('Choose Your Plan');
            $table->string('pricing_subtitle')->default('Select the plan that best matches your needs');
            $table->text('offer_text')->nullable();
            
            // Dashboard Content
            $table->string('dashboard_text')->default('Welcome Back');
            $table->string('dashboard_subtext')->default('Here\'s what\'s happening with your account today');
            
            // System Defaults
            $table->string('currency')->default('USD');
            $table->string('currency_symbol')->default('$');
            $table->string('timezone')->default('UTC');
            
            $table->timestamps();
        });

        // Insert default settings
        DB::table('system_settings')->insert([
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
