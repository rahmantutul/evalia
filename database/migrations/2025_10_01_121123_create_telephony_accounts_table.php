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
        Schema::create('telephony_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable(); // nullable now
            $table->string('provider'); // avaya, twilio, etc.
            $table->string('username');
            $table->text('password'); // encrypted
            $table->string('base_url')->nullable(); // if needed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telephony_accounts');
    }
};
