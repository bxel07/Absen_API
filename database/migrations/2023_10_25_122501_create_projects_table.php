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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_member_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            // $table->string('name');
            $table->string('project_title');
            $table->dateTime('deadline');
            $table->text('description');
            $table->integer('reward_point');
            $table->string('file')->nullable();
            $table->enum('status', ['to-do', 'in progress', 'completed']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
