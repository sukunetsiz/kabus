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
        Schema::table('vendor_profiles', function (Blueprint $table) {
            $table->boolean('private_shop_mode')->default(false)->after('vacation_mode');
        });

        Schema::create('private_shops', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('vendor_id')->nullable();
            $table->string('vendor_reference_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('users')->onDelete('cascade');
            
            // Ensure a user can't add the same vendor reference id twice
            $table->unique(['user_id', 'vendor_reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('private_shops');
        
        Schema::table('vendor_profiles', function (Blueprint $table) {
            $table->dropColumn('private_shop_mode');
        });
    }
};