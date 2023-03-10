<?php

use App\Models\LastFmArtist;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() : void
    {
        Schema::create('last_fm_songs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(LastFmArtist::class);
            $table->string('mbid');
            $table->string('name');
            $table->string('url');
            $table->json('image');
            $table->json('streamable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::dropIfExists('last_fm_songs');
    }
};
