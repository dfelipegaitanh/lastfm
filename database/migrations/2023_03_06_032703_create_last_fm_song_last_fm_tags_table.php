<?php

use App\Models\LastFmSong;
use App\Models\LastFmTag;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() : void
    {
        Schema::create('last_fm_song_last_fm_tag', function (Blueprint $table) {
            $table->foreignIdFor(LastFmSong::class)
                  ->constrained()
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignIdFor(LastFmTag::class)
                  ->constrained()
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::dropIfExists('last_fm_song_last_fm_tag');
    }
};
