<?php

use App\Models\LastFmArtist;
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
        Schema::create('last_fm_artist_last_fm_tag', function (Blueprint $table) {
            $table->foreignIdFor(LastFmArtist::class);
            $table->foreignIdFor(LastFmTag::class);
            $table->integer('count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::dropIfExists('last_fm_artist_last_fm_tag');
    }
};
