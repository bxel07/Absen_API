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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('fullname', 100)->nullable();
            $table->string('email', 100)->unique()->nullable();
            $table->string('password')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender',['laki-laki', 'perempuan'])->nullable();
            $table->string('contact', 13)->nullable();
            $table->enum('religion', ['Islam', 'Kristen', 'Hindu', 'Budha', 'Konghucu'])->nullable();
            $table->string('image_profile')->nullable();
            $table->foreignId('role_id')->nullable()->constrained('roles');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
