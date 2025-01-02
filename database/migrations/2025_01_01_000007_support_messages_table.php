<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('support_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('support_request_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->text('message');
            $table->boolean('is_admin_reply')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('support_messages');
    }
};