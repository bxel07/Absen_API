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
        Schema::create('approved_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('shift_request_id')->nullable()->references('id')->on('shift_requests')->onDelete('cascade');
            $table->foreignId('leave_request_id')->nullable()->references('id')->on('leave_requests')->onDelete('cascade');
            $table->foreignId('attendance_request_id')->nullable()->references('id')->on('attendance_requests')->onDelete('cascade');
            $table->enum('status', ['approved', 'pending', 'rejected']);
            $table->boolean('reward_flag');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approved_requests');
    }
};
