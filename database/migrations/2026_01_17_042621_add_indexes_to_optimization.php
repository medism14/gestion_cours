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
        Schema::table('users', function (Blueprint $table) {
            $table->index('first_name');
            $table->index('last_name');
            $table->index('role');
        });

        Schema::table('annonces', function (Blueprint $table) {
            $table->index('title');
            $table->index('date_expiration');
        });

        Schema::table('resources', function (Blueprint $table) {
            $table->index('section');
        });

        Schema::table('files', function (Blueprint $table) {
            $table->index('filename');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['first_name']);
            $table->dropIndex(['last_name']);
            $table->dropIndex(['role']);
        });

        Schema::table('annonces', function (Blueprint $table) {
            $table->dropIndex(['title']);
            $table->dropIndex(['date_expiration']);
        });

        Schema::table('resources', function (Blueprint $table) {
            $table->dropIndex(['section']);
        });

        Schema::table('files', function (Blueprint $table) {
            $table->dropIndex(['filename']);
        });
    }
};
