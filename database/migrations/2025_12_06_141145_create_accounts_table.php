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
      Schema::create('accounts', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

        // Basic Account Metadata
        $table->string('nickname')->nullable();
        $table->string('account_name')->nullable();
        $table->string('email')->nullable();
        $table->string('mobile')->nullable();
        $table->string('password')->nullable();
        $table->string('client_id');             // Smart-API Client ID
        $table->string('pin')->nullable();             // Smart-API Client ID
        $table->string('api_key')->nullable();               // API Key
        $table->string('client_secret')->nullable();
        $table->string('totp_secret')->nullable();

        // Token / Session Data
        $table->text('session_token')->nullable();
        $table->text('refresh_token')->nullable();
        $table->text('feed_token')->nullable();
        $table->timestamp('token_expiry')->nullable();

        // Status & Tracking
        $table->boolean('is_active')->default(true); // enable/disable
        $table->string('status')->default('idle');   // idle, logged_in, error, etc
        $table->text('last_error_code')->nullable();
        $table->text('last_error')->nullable();
        $table->timestamp('last_login_at')->nullable();

        // Account Balance
        $table->decimal('net')->nullable();
        $table->decimal('amount_used')->nullable();

        $table->timestamps();
      });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
