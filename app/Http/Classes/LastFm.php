<?php

namespace App\Http\Classes;

use Barryvanveen\Lastfm\Constants;
use Barryvanveen\Lastfm\Exceptions\InvalidPeriodException;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class LastFm extends \Barryvanveen\Lastfm\Lastfm
{

    protected Carbon $initDate;
    protected Carbon $endDate;
    protected string $username;
    protected int    $limit;
    protected int    $min_plays;

    public function __construct(Client $client)
    {
        parent::__construct($client, config('lastfm.api_key'));
    }

    /**
     * @return Collection
     */
    public function getUserWeeklyTopTracks() : Collection
    {
        return $this->userWeeklyTopTracks($this->username, Carbon::today()->subWeek())
                    ->getData();
    }


    /**
     * @return Collection
     */
    public function getData() : Collection
    {
        return collect(parent::get())
            ->toCollection()
            ->minPlays($this->min_plays);
    }

    /**
     * @return array
     */
    public
    function getUserTopAlbums() : array
    {
        return parent::userTopAlbums($this->username)
                     ->get();
    }

    /**
     * @return array
     * @throws InvalidPeriodException
     */
    public
    function getUserTopTracks() : array
    {
        return parent::userTopTracks($this->username)
                     ->period(Constants::PERIOD_WEEK)
                     ->limit(10)
                     ->get();
    }

    /**
     * @param  string  $username
     */
    public
    function setUsername(string $username) : void
    {
        $this->username = $username;
    }


    /**
     * @param  string|null  $limit
     * @return void
     */
    public function setLimit(?string $limit) : void
    {
        $this->limit(is_null($limit)
                         ? (int) config('lastfm.limit')
                         : (int) $limit);
    }

    /**
     * @param  Collection  $dates
     */
    public function setDates(Collection $dates) : void
    {
        $this->setInitDate($dates->get('initDate'));
        $this->setEndDate($dates->get('endDate'));
    }

    /**
     * @param  Carbon  $initDate
     */
    public function setInitDate(Carbon $initDate) : void
    {
        $this->initDate = $initDate;
    }

    /**
     * @param  Carbon  $endDate
     */
    public function setEndDate(Carbon $endDate) : void
    {
        $this->endDate = $endDate;
    }

    /**
     * @param  string|null  $min_plays
     */
    public function setMinPlays(?string $min_plays) : void
    {
        $this->min_plays = is_null($min_plays)
            ? (int) config('lastfm.min_plays')
            : (int) $min_plays;
    }

    /**
     * @return array
     */
    protected
    function getUserInfo() : array
    {
        return parent::userInfo($this->username)
                     ->get();
    }

    /**
     * @return array|bool
     */
    protected
    function getNowListening() : bool|array
    {
        return parent::nowListening($this->username);
    }

    /**
     * @param  Carbon  $initDate
     * @param  int  $months
     * @return void
     */
    public
    function getUserTopTracksPeriodTimeByMonths(Carbon $initDate, int $months = 1)
    {
        $songs = collect();
        while ($initDate->year <= 2023) {
            $this->query = array_merge($this->query, [
                'method' => 'user.getWeeklyTrackChart',
                'user'   => $this->username,
                'limit'  => $this->limit,
                'from'   => $initDate->format('U'),
                'to'     => $initDate->addMonths($months)->subSecond()->format('U'),
            ]);

            $this->pluck = 'weeklytrackchart.track';
            dd($this->getCollection());
            $songs->push(
                $this->getCollection()
                     ->filter(function ($song) {
                         return collect($song)->get('playcount', 0) >= 5 && collect($song)->get('playcount', 0) <= 10;
                     }));

            $initDate->addSecond();
        }


        dd($songs->flatten(1));
    }

    /**
     * @return Collection
     */
    public
    function getCollection() : Collection
    {
        return collect($this->get());
    }

}
