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
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->integer('role');
            $table->boolean('first_connection');
            $table->bigInteger('notifs')->nullable();
            $table->string('password');
            $table->unsignedBigInteger('level_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
        
            $table->foreign('level_id')->references('id')->on('levels')->onDelete('cascade');
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
