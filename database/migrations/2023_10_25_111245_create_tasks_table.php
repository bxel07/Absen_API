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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            // $table->integer('approved_tasks_id')->nullable();
            $table->string('name');
            $table->integer('project_id');
            $table->string('project_title');
            // $table->dateTime('deadline')->nullable();
            $table->text('description');
            // $table->string('file')->nullable();
            // $table->enum('status', ['to-do', 'in progress', 'completed'])->default('to-do');
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
