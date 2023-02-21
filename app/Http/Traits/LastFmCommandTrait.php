<?php

namespace App\Http\Traits;

use App\Http\Classes\LastFm;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

trait LastFmCommandTrait
{

    /**
     * @return string
     */
    public function getUsername() : mixed
    {
        return is_null($this->argument('user')) ? config('lastfm.user') : $this->argument('user');
    }

    /**
     * @return Carbon
     */
    public function getInitDate() : Carbon
    {
        return Carbon::create(
            $this->getInitYear($this->option('initYear')) ,
            $this->getInitMonth($this->option('initMonth'))
        );
    }

    /**
     * @param  string|null  $initMonth
     * @return string
     */
    public function getInitMonth(?string $initMonth) : string
    {
        return is_null($initMonth) ? config('lastfm.init_month') : $initMonth;
    }

    /**
     * @param  string|null  $initYear
     * @return string
     */
    public function getInitYear(?string $initYear) : string
    {
        return is_null($initYear) ? config('lastfm.init_year') : $initYear;
    }

    /**
     * @param  LastFm  $lastFm
     * @return void
     */
    public function setUpLastFm(LastFm &$lastFm){
        $lastFm->setUsername($this->getUsername());
    }

}
