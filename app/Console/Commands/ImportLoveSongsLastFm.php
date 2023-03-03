<?php

namespace App\Console\Commands;

use App\Http\Classes\LastFm;
use App\Http\Traits\LastFmCommandTrait;
use Illuminate\Console\Command;

class ImportLoveSongsLastFm extends Command
{

    use LastFmCommandTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lastfm:loveSongs {user?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Love Songs';

    /**
     * @param  LastFm  $lastFm
     * @return void
     */
    public function handle(LastFm $lastFm) : void
    {
        $this->setUpLastFmLoveSongs($lastFm);
        $lastFm->getLovedTracks($this);
    }
}
