<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorPaymentSubaddressesTable extends Migration
{
    public function up()
    {
        Schema::create('vendor_payment_subaddresses', function (Blueprint $table) {
            $table->id();
            $table->string('address');
            $table->unsignedInteger('address_index');
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->decimal('total_received', 18, 12)->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('expires_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendor_payment_subaddresses');
    }
}