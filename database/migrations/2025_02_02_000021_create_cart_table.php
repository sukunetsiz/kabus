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
        Schema::create('cart', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('product_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('price', 10, 2); // Store the price at the time of adding to cart
            $table->json('selected_delivery_option'); // Store the selected delivery option
            $table->json('selected_bulk_option')->nullable(); // Store the selected bulk option if any
            $table->text('encrypted_message')->nullable(); // Store PGP encrypted messages
            $table->timestamps();

            // Indexes for better performance
            $table->index('user_id');
            $table->index('product_id');
            
            // Unique constraint to prevent duplicate products in cart
            $table->unique(['user_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart');
    }
};
