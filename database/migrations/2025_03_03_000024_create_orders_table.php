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
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('vendor_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('unique_url', 30)->unique();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('commission', 10, 2);
            $table->decimal('total', 10, 2);
            $table->string('status');
            $table->text('shipping_address')->nullable();
            $table->text('delivery_option')->nullable();
            $table->text('encrypted_message')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->boolean('is_delivered')->default(false);
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('completed_at')->nullable();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('product_id')->constrained()->onDelete('cascade');
            $table->string('product_name');
            $table->text('product_description');
            $table->decimal('price', 10, 2);
            $table->integer('quantity');
            $table->string('measurement_unit')->nullable();
            $table->json('delivery_option')->nullable();
            $table->json('bulk_option')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};