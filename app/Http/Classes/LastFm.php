<?php

namespace App\Http\Classes;

use App\Http\Traits\LastFmTrait;
use App\Models\LastFmUser;
use Barryvanveen\Lastfm\Constants;
use Barryvanveen\Lastfm\Exceptions\InvalidPeriodException;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class LastFm extends \Barryvanveen\Lastfm\Lastfm
{

    use LastFmTrait;

    protected Carbon     $initDate;
    protected Carbon     $endDate;
    protected string     $username;
    protected int        $limit;
    protected int        $min_plays;
    protected LastFmUser $lastFmUser;

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
     * @param  int  $limit
     * @return Collection
     */
    public function getLovedTracks(int $limit = 100) : Collection
    {

        $this->queryLoveTracks($limit);
        $this->pluck = 'lovedtracks';
        $attr        = $this->getAttr();
        $this->pluck = 'lovedtracks.track';
        $songs       = collect(collect($this->data)->get('track', []));

        for ($i = $attr->get('page', 0) + 1; $i <= $attr->get('totalPages', 0); $i++) {
            $this->page($i);
            $this->queryLoveTracks($limit);
            $this->getFullData()
                 ->each(function ($song) use ($songs) {
                     $songs->push($song);
                 });
        }

        dd($songs->count());
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
     * @return Collection
     */
    public function getUserInfo() : Collection
    {
        return parent::userInfo($this->username)
                     ->getFullData();
    }


    /**
     * @param  int  $limit
     */
    public function queryLoveTracks(int $limit = 1)
    {
        $this->query = array_merge($this->query, [
            'method' => 'user.getLovedTracks',
            'user'   => $this->username,
            'limit'  => $limit,
        ]);
    }

    /**
     * @param  LastFmUser  $lastFmUser
     */
    public function setLastFmUser(LastFmUser $lastFmUser) : void
    {
        $this->lastFmUser = $lastFmUser;
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
