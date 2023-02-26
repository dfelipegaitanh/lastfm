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
        Schema::create('last_fm_users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('age');
            $table->string('subscriber');
            $table->string('realname');
            $table->string('bootstrap');
            $table->bigInteger('playcount');
            $table->bigInteger('artist_count');
            $table->unsignedInteger('playlists');
            $table->unsignedInteger('track_count');
            $table->unsignedInteger('album_count');
            $table->json('image');
            $table->json('registered');
            $table->string('country');
            $table->string('gender');
            $table->string('url');
            $table->string('type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('last_fm_users');
    }
};
