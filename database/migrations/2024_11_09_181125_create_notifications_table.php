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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('message');
            $table->string('target_role')->nullable(); // For role-specific messages (admin, vendor, or null for all users)
            $table->timestamps();
        });

        // Create notification_user pivot table
        Schema::create('notification_user', function (Blueprint $table) {
            $table->foreignUuid('notification_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->timestamps(); // Add timestamps columns
            $table->primary(['notification_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_user');
        Schema::dropIfExists('notifications');
    }
};