<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\LastFmArtist;
use App\Models\LastFmArtistLastFmTag;
use App\Models\LastFmLoveSong;
use App\Models\LastFmSong;
use App\Models\LastFmTag;
use App\Models\LastFmUser;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run() : void
    {
        LastFmArtistLastFmTag::truncate();
        LastFmTag::truncate();
        LastFmArtist::truncate();
        LastFmLoveSong::truncate();
        LastFmSong::truncate();
        LastFmUser::truncate();
    }
}
