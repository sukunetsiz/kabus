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
        Schema::create('advertisements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('slot_number')->unsigned();
            $table->integer('duration_days')->unsigned();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->string('payment_identifier', 64)->unique();
            $table->string('payment_address');
            $table->integer('payment_address_index')->unsigned();
            $table->decimal('total_received', 12, 12)->default(0);
            $table->decimal('required_amount', 12, 12);
            $table->boolean('payment_completed')->default(false);
            $table->timestamp('payment_completed_at')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();
            
            // Ensure only one active advertisement per slot
            $table->unique(['slot_number', 'starts_at', 'ends_at'], 'unique_active_slot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};