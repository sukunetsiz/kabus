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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->string('name');
            $table->string('slug', 80)->unique();  // Added slug field for unique product URLs
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->json('delivery_options')->nullable(false); // Store array of delivery options with description and price
            $table->json('bulk_options')->nullable(); // Store array of bulk options with amount and price (optional)
            $table->string('type'); // 'digital', 'cargo', or 'deaddrop'
            $table->string('product_picture')->nullable(); // Added product picture field
            $table->json('additional_photos')->nullable(); // Store up to 3 additional photos
            $table->boolean('active')->default(true);
            $table->unsignedInteger('stock_amount')->default(0); // Track product stock
            $table->string('measurement_unit'); // Unit of measurement (g, kg, ml, l, cm, m, in, ft, m², piece, dozen, hour, day, month)
            $table->string('ships_from')->default('Worldwide'); // Country where the product ships from
            $table->string('ships_to')->default('Worldwide'); // Country where the product can be shipped to
            $table->timestamps();
            $table->softDeletes();

            // Index for faster queries
            $table->index('type');
            $table->index('active');
            $table->index('category_id');
            $table->index('slug');  // Added index for slug lookups
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
