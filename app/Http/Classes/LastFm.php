<?php

namespace App\Http\Classes;

use Barryvanveen\Lastfm\Constants;
use Barryvanveen\Lastfm\Exceptions\InvalidPeriodException;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class LastFm extends \Barryvanveen\Lastfm\Lastfm
{

    private string     $username;
    private array      $userInfo;
    private array|bool $nowListening;
    private int        $limit;
    private int        $min_plays;

    public function __construct(Client $client)
    {
        parent::__construct($client , config('lastfm.api_key'));
        $this->limit        = config('lastfm.limit');
//        $this->username     = config('lastfm.user');
        $this->min_plays    = config('lastfm.min_plays');
//        $this->userInfo     = $this->getUserInfo();
//        $this->nowListening = $this->getNowListening();
    }

    /**
     * @return array
     */
    public function getUserWeeklyTopTracks($initDate) : array
    {
        return $this->userWeeklyTopTracks($this->username , $initDate)
                    ->get();
    }

    /**
     * @return array
     */
    public function getUserTopAlbums() : array
    {
        return parent::userTopAlbums($this->username)
                     ->get();
    }

    /**
     * @return array
     * @throws InvalidPeriodException
     */
    public function getUserTopTracks() : array
    {
        return parent::userTopTracks($this->username)
                     ->period(Constants::PERIOD_WEEK)
                     ->limit(10)
                     ->get();
    }

    /**
     * @param  string  $username
     */
    public function setUsername(string $username) : void
    {
        $this->username = $username;
    }

    /**
     * @return array
     */
    protected function getUserInfo() : array
    {
        return parent::userInfo($this->username)
                     ->get();
    }

    /**
     * @return array|bool
     */
    protected function getNowListening() : bool|array
    {
        return parent::nowListening($this->username);
    }

    /**
     * @param  Carbon  $initDate
     * @param  int  $months
     * @return void
     */
    public function getUserTopTracksPeriodTimeByMonths(Carbon $initDate , int $months = 1)
    {
        $songs = collect();
        while ($initDate->year <= 2023) {
            $this->query = array_merge($this->query , [
                'method' => 'user.getWeeklyTrackChart' ,
                'user'   => $this->username ,
                'limit'  => $this->limit ,
                'from'   => $initDate->format('U') ,
                'to'     => $initDate->addMonths($months)->subSecond()->format('U') ,
            ]);

            $this->pluck = 'weeklytrackchart.track';
            dd($this->getCollection());
            $songs->push(
                $this->getCollection()
                     ->filter(function ($song) {
                         return collect($song)->get('playcount' , 0) >= 5 && collect($song)->get('playcount' , 0) <= 10;
                     }));

            $initDate->addSecond();
        }


        dd($songs->flatten(1));
    }

    /**
     * @return Collection
     */
    public function getCollection() : Collection
    {
        return collect($this->get());
    }

}
