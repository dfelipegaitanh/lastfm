<?php

namespace App\Console\Commands;

use App\Http\Classes\LastFm;
use App\Http\Traits\LastFmCommandTrait;
use App\Models\LastFmPeriodTime;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

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
        $lastFm->getUserWeeklyChartList()
               ->each(function (Collection $chartPeriod) use ($lastFm) {

                   $periodTime       = LastFmPeriodTime::firstOrNew(
                       [
                           'dateStart' => $this->getPeriodTimeDateStart($chartPeriod),
                           'dateEnd'   => $this->getPeriodTimeDateEnd($chartPeriod),
                       ]
                   );
                   $periodTime->type = 'weekly';
                   $periodTime->save();

                   $lastFm->getWeeklyTrackChart($periodTime)
                          ->each(function (Collection $data) use ($periodTime) {
                              if ($data->isNotEmpty()) {
                                  $this->info('Period From '.$periodTime->dateStart.' To '.$periodTime->dateEnd.' have '.$data->count().' songs');
                                  dd(
                                      $data,
                                  );
                              }
                              else {
                                  $this->error('Period From '.$periodTime->dateStart.' To '.$periodTime->dateEnd.' have '.$data->count().' songs');
                              }

                          });

               });

    }
}
