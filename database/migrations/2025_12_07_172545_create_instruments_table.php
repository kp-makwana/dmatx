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
        Schema::create('instruments', function (Blueprint $table) {
            $table->id();
            $table->string('token');
            $table->string('symbol', 50);
            $table->string('name', 150)->nullable();
            $table->string('expiry', 50)->nullable();
            $table->string('strike')->nullable();
            $table->integer('lotsize')->nullable();
            $table->string('instrumenttype', 50)->nullable();
            $table->string('exch_seg', 20);
            $table->string('tick_size')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instruments');
    }
};
