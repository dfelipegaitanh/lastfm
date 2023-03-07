<?php

namespace App\Console\Commands;

use App\Http\Classes\LastFm;
use App\Http\Traits\LastFmCommandTrait;
use Illuminate\Console\Command;

class ImportAllChartWeeklyLastFm extends Command
{

    use LastFmCommandTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lastfm:chartWeekly {user?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @param  LastFm  $lastFm
     * @return void
     */
    public function handle(LastFm $lastFm) : void
    {
        $this->setUpChartWeeklyLastFm($lastFm);
        $lastFm->getChartWeekly($this);
    }
}
