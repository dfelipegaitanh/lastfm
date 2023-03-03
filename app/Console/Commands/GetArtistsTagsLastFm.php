<?php

namespace App\Console\Commands;

use App\Http\Classes\LastFm;
use App\Models\LastFmArtist;
use Illuminate\Console\Command;

class GetArtistsTagsLastFm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lastfm:getArtistsTags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Tags from Artists stored in the DB';

    /**
     * Execute the console command.
     */
    public function handle(LastFm $lastFm) : void
    {
        dd(LastFmArtist::all());
    }
}
