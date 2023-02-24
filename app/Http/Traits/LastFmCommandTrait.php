<?php

namespace App\Http\Traits;

use App\Http\Classes\LastFm;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

trait LastFmCommandTrait
{

    use DateTraits;

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
                           'initDate' => $initDate ,
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
    }

    /**
     * @return false|Carbon
     */
    public function getInitDate() : Carbon|false
    {
        return Carbon::create(
            (int) ($this->option('initYear') ?? config('lastfm.init_year')) ,
            $this->validMonth($this->option('initMonth')) ?? 1);
    }

    /**
     * @param  Carbon  $initDate
     * @return Carbon
     */
    public function getEndDate(Carbon $initDate) : Carbon
    {
        $endDate = Carbon::create(
            (int) ($this->option('endYear') ?? config('lastfm.end_year')) ,
            $this->validMonth($this->option('endMonth') , true) ?? 12);

        if ($initDate->greaterThan($endDate)) {
            $endDate = $initDate;
        }

        return $endDate;
    }

}
