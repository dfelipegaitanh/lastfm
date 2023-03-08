<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\LastFmArtist;
use App\Models\LastFmArtistLastFmTag;
use App\Models\LastFmArtistStat;
use App\Models\LastFmImageArtist;
use App\Models\LastFmLoveSong;
use App\Models\LastFmPeriodTime;
use App\Models\LastFmSong;
use App\Models\LastFmSongLastFmTag;
use App\Models\LastFmSongStat;
use App\Models\LastFmTag;
use App\Models\LastFmUser;
use App\Models\LastFmUserStat;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run() : void
    {

        \Schema::withoutForeignKeyConstraints(function () {
            LastFmArtist::truncate();
            LastFmPeriodTime::truncate();
            LastFmArtistLastFmTag::truncate();
            LastFmArtistStat::truncate();
            LastFmImageArtist::truncate();
            LastFmUserStat::truncate();
            LastFmTag::truncate();
            LastFmArtist::truncate();
            LastFmLoveSong::truncate();
            LastFmSong::truncate();
            LastFmUser::truncate();
            LastFmSongLastFmTag::truncate();
            LastFmSongStat::truncate();
        });
    }
}
