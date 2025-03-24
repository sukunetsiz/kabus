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
        Schema::create('disputes', function (Blueprint $table) {
            $table->string('id', 30)->primary();
            $table->uuid('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->string('status')->default('active'); // active, vendor_prevails, buyer_prevails
            $table->text('reason')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->uuid('resolved_by')->nullable();
            $table->foreign('resolved_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('dispute_messages', function (Blueprint $table) {
            $table->string('id', 30)->primary();
            $table->string('dispute_id', 30);
            $table->foreign('dispute_id')->references('id')->on('disputes')->onDelete('cascade');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('message');
            $table->timestamps();
        });

        // Add disputed status to the orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_disputed')->default(false);
            $table->timestamp('disputed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('is_disputed');
            $table->dropColumn('disputed_at');
        });
        
        Schema::dropIfExists('dispute_messages');
        Schema::dropIfExists('disputes');
    }
};