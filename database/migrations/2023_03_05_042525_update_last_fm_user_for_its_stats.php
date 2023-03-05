<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() : void
    {
        Schema::table('last_fm_users', function (Blueprint $table) {
            $table->dropColumn(
                [
                    'playcount',
                    'artist_count',
                    'playlists',
                    'track_count',
                    'album_count',
                ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::table('last_fm_users', function (Blueprint $table) {
            $table->integer('playcount');
            $table->integer('artist_count');
            $table->integer('playlists');
            $table->integer('track_count');
            $table->integer('album_count');
        });
    }
};
