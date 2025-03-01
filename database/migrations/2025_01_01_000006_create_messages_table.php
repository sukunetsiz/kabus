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
        Schema::create('messages', function (Blueprint $table) {
            $table->string('id', 30)->primary();
            $table->string('conversation_id', 30)->nullable();
            $table->uuid('sender_id')->nullable();
            $table->uuid('user_id_1')->nullable();
            $table->uuid('user_id_2')->nullable();
            $table->text('content')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_id_1')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_id_2')->references('id')->on('users')->onDelete('cascade');

            // Add indexes
            $table->index('conversation_id');
            $table->index('sender_id');
            $table->index('user_id_1');
            $table->index('user_id_2');
            $table->index('created_at');
            $table->index('last_message_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};