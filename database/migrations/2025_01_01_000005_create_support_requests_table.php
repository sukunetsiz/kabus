<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('support_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ticket_id', 30)->unique()->nullable();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->string('title')->nullable();
            $table->enum('status', ['open', 'in_progress', 'closed'])->default('open');
            $table->text('message')->nullable();
            $table->boolean('is_admin_reply')->default(false);
            $table->foreignUuid('parent_id')->nullable()->references('id')->on('support_requests')->onDelete('cascade');
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['parent_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('support_requests');
    }
};