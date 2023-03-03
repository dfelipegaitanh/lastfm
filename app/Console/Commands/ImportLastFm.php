<?php

namespace App\Console\Commands;

use App\Http\Classes\LastFm;
use App\Http\Traits\LastFmCommandTrait;
use Illuminate\Console\Command;

class ImportLastFm extends Command
{

    use LastFmCommandTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lastfm:import {user?} {--initYear=} {--initMonth=} {--endYear=} {--endMonth=} {--limit=} {--minPlays=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import songs list from last.fm';

    /**
     * @param  LastFm  $lastFm
     * @return void
     */
    public function handle(LastFm $lastFm) : void
    {
        $this->setUpLastFm($lastFm);
        dd($lastFm->getUserWeeklyTopTracks());
    }

}
