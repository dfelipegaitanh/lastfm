<?php

namespace App\Http\Controllers;

use App\Http\Classes\LastFm;
use Illuminate\Support\Carbon;

class LastFmController extends Controller
{

    private Carbon|false $initDate;

    public function __construct(){
        $this->initDate = Carbon::create(2006,4);
    }

    /**
     * @param  LastFm  $lastFm
     * @return void
     */
    public function index(LastFm $lastFm) {
        $lastFm->getUserTopTracksPeriodTimeByMonths($this->initDate, 1);
//        dd($lastFm->getUserWeeklyTopTracks($this->initDate), $this->initDate->format('Y-m-d'));
    }

    /**
     * @param  LastFm  $lastFm
     * @return void
     */
    public function topAlbums(LastFm $lastFm) : void
    {
        dd($lastFm->getUserTopAlbums());
    }

}
