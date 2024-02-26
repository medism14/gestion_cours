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
            $table->string('sexe')->nullable();
            $table->boolean('first_connection');
            $table->bigInteger('notifs')->nullable();
            $table->datetime('notif_viewed')->nullable();
            $table->bigInteger('annonces')->nullable();
            $table->datetime('annonce_viewed')->nullable();
            $table->bigInteger('messages')->nullable();
            $table->datetime('message_viewed_at')->nullable();
            $table->string('password');
            $table->rememberToken();
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
