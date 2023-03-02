<?php

namespace App\Http\Classes;

use App\Console\Commands\ImportLoveSongsLastFm;
use App\Http\Traits\LastFmDBTrait;
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
    use LastFmDBTrait;

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
     * @param  ImportLoveSongsLastFm  $console
     * @return void
     */
    public function getLovedTracks(ImportLoveSongsLastFm $console) : void
    {

        $this->getLovedTracksCollect()
             ->each(function (Collection $song) use ($console) {
                 $lastFmArtist = $this->getLastFmArtist($this->getLastFmArtistFromAPI($song));
                 $lastFmSong   = $this->getLastFmSong($song, $lastFmArtist);
                 $this->getLastFmLoveSong($lastFmSong, $song);

                 $console->info("Artist: {$lastFmArtist->name}");
                 $console->warn("Song: {$song->get('name', '')}");
                 $console->newLine();
             });
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
     */
    public function userLoveTracks($pluck = 'lovedtracks')
    {
        $this->query = array_merge($this->query, [
            'method' => 'user.getLovedTracks',
            'user'   => $this->username,
            'limit'  => config('lastfm.limit_loves'),
        ]);
        $this->pluck = $pluck;

        return $this->getFullData();
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

}
