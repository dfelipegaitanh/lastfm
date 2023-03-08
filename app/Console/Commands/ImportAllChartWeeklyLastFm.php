<?php

namespace App\Console\Commands;

use App\Http\Classes\LastFm;
use App\Http\Traits\LastFmCommandTrait;
use Illuminate\Console\Command;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ImportAllChartWeeklyLastFm extends Command
{

    use LastFmCommandTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lastfm:chartWeekly {user?} {--reProcess=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @param  LastFm  $lastFm
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(LastFm $lastFm) : void
    {
        $this->setUpChartWeeklyLastFm($lastFm);
        $lastFm->getChartWeekly($this);
    }
}
