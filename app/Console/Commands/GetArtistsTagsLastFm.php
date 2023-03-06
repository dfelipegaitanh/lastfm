<?php

namespace App\Console\Commands;

use App\Http\Classes\LastFm;
use App\Http\Traits\LastFmCommandTrait;
use App\Http\Traits\LastFmDBTrait;
use App\Models\LastFmArtist;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class GetArtistsTagsLastFm extends Command
{

    use LastFmCommandTrait;
    use LastFmDBTrait;

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
        return;
        // Deprecated
        LastFmArtist::all()
                    ->each(function (LastFmArtist $lastFmArtist) use ($lastFm) {
                        $this->info("Artist: ".$lastFmArtist->name);
                        $lastFm->getArtistTags($lastFmArtist)
                               ->each(function (Collection $tag) use ($lastFmArtist) {
                                   $this->createArtistTag($tag, $lastFmArtist);
                                   $this->warn("Tag: ".$tag->get('name').". Count: ".$tag->get('count'));
                               });
                        $this->newLine();
                    });

    }

}
