<?php

use App\Models\LastFmArtist;
use App\Models\LastFmUser;
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
        Schema::create('last_fm_artist_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(LastFmArtist::class);
            $table->foreignIdFor(LastFmUser::class);
            $table->unsignedInteger('userplaycount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('last_fm_artist_stats');
    }
};
