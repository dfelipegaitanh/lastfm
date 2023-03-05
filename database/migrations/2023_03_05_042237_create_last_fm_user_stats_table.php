<?php

use App\Models\LastFmUser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() : void
    {
        Schema::create('last_fm_user_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(LastFmUser::class);
            $table->unsignedInteger('playcount');
            $table->unsignedInteger('artist_count');
            $table->unsignedInteger('playlists');
            $table->unsignedInteger('track_count');
            $table->unsignedInteger('album_count');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::dropIfExists('last_fm_user_stats');
    }
};
