<?php

use App\Models\LastFmPeriodTime;
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
        Schema::create('last_fm_song_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(LastFmSong::class)
                  ->constrained()
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignIdFor(LastFmUser::class)
                  ->constrained()
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignIdFor(LastFmPeriodTime::class)
                  ->constrained()
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->unsignedInteger('userplaycount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::dropIfExists('last_fm_song_stats');
    }
};
