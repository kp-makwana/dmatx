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
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();

          $table->string('full_name');
          $table->string('email');
          $table->text('message');
          $table->string('ip_address', 45)->nullable();

          // Admin reply (revert) fields
          $table->boolean('is_reverted')->default(false);
          $table->timestamp('reverted_at')->nullable();
          $table->text('revert_message')->nullable();
          $table->unsignedBigInteger('reverted_by')->nullable();
          $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
