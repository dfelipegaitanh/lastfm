<?php

namespace App\Http\Traits;

use App\Http\Classes\LastFm;
use App\Models\LastFmPeriodTime;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

trait LastFmCommandTrait
{

    use DateTraits;
    use LastFmTrait;
    use LastFmDBTrait;

    /**
     * @return string
     */
    public function getUsername() : mixed
    {
        return is_null($this->argument('user')) ? config('lastfm.user') : $this->argument('user');
    }

    /**
     * @return Collection
     */
    public function getDates() : Collection
    {
        $initDate = $this->getInitDate();
        return collect([
                           'initDate' => $initDate,
                           'endDate'  => $this->getEndDate($initDate)
                       ]);
    }


    /**
     * @param  LastFm  $lastFm
     * @return void
     */
    public function setUpLastFm(LastFm &$lastFm) : void
    {
        $lastFm->setUsername($this->getUsername());
        $lastFm->setDates($this->getDates());
        $lastFm->setLimit($this->option('limit'));
        $lastFm->setMinPlays($this->option('minPlays'));
    }


    public function setUpLastFmLoveSongs(LastFm &$lastFm) : void
    {
        $lastFm->setUsername($this->getUsername());
        $lastFm->setLastFmUser($this->getLastFmUser($lastFm->getUserInfo()));

        $dateStart = $lastFm->getLastFmUser()->get('dateFirstScrobble');
        $dateEnd   = Carbon::now()->format($this->dateFormat());
        $this->createPeriodTime($dateStart, $dateEnd);
    }

    /**
     * @return false|Carbon
     */
    public function getInitDate() : Carbon|false
    {
        return Carbon::create(
            ($this->option('initYear') ?? config('lastfm.init_year')),
            $this->validMonth($this->option('initMonth')) ?? 1);
    }

    /**
     * @param  Carbon  $initDate
     * @return Carbon
     */
    public function getEndDate(Carbon $initDate) : Carbon
    {
        $endDate = Carbon::create(
            ($this->option('endYear') ?? config('lastfm.end_year')),
            $this->validMonth($this->option('endMonth'), true) ?? 12);

        if ($initDate->greaterThan($endDate)) {
            $endDate = $initDate;
        }

        return $endDate;
    }

    /**
     * @return string
     */
    public function dateFormat() : string
    {
        return 'Y-m-d H:i:s';
    }

    /**
     * @param  string  $dateStart
     * @param  string  $dateEnd
     * @return void
     */
    public function createPeriodTime(string $dateStart, string $dateEnd) : void
    {
        session('periodTime', LastFmPeriodTime::firstOrCreate(
            [
                'dateStart' => $dateStart,
                'dateEnd'   => $dateEnd
            ]));
    }

}
