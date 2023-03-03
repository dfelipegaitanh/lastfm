<?php

use App\Models\LastFmSong;
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
        Schema::create('last_fm_love_songs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(LastFmSong::class);
            $table->foreignIdFor(LastFmUser::class);
            $table->unsignedDouble('uts');
            $table->string('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::dropIfExists('last_fm_love_songs');
    }
};
