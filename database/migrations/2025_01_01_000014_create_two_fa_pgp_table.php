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
        Schema::table('pgp_keys', function (Blueprint $table) {
            $table->boolean('two_fa_enabled')->default(false)->after('verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pgp_keys', function (Blueprint $table) {
            $table->dropColumn('two_fa_enabled');
        });
    }
};