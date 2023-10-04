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
        Schema::create('schedule_shift', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->references('id')->on('shifts');
            $table->foreignId('schedule_id')->references('id')->on('schedules');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->enum('initial_shift', ['national_data', 'holiday']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_shift');
    }
};
