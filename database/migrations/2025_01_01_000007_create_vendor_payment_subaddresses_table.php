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
            $table->string('identifier', 30)->unique();
            $table->string('address');
            $table->unsignedInteger('address_index');
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->decimal('total_received', 18, 12)->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('expires_at');
            
            // Application fields
            $table->text('application_text')->nullable();
            $table->enum('application_status', ['waiting', 'accepted', 'denied'])->nullable();
            $table->json('application_images')->nullable(); // Store up to 4 image paths
            $table->timestamp('application_submitted_at')->nullable();
            $table->timestamp('admin_response_at')->nullable();
            $table->decimal('refund_amount', 20, 12)->nullable();
            $table->string('refund_address')->nullable();
            $table->boolean('payment_completed')->default(false);
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendor_payment_subaddresses');
    }
}