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
        // Migration for caching API users
        Schema::create('users', function (Blueprint $table) {
            $table->string('id')->primary(); // UUID from API
            $table->string('username');
            $table->string('email');
            $table->string('full_name')->nullable();
            $table->string('position')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('role')->nullable();
            $table->string('company_id')->nullable();
            $table->string('supervisor_id')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
