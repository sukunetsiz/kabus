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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('username')->unique();
            $table->string('password');
            $table->text('mnemonic')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->string('password_reset_token')->nullable();
            $table->timestamp('password_reset_expires_at')->nullable();
            $table->text('reference_id')->nullable();
            $table->uuid('referred_by')->nullable();
            $table->foreign('referred_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
            $table->rememberToken();
            $table->timestamps();
            $table->index('username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
