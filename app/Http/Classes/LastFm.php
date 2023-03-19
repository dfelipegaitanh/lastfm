<?php

namespace App\Http\Classes;

use App\Console\Commands\ImportAllChartWeeklyLastFm;
use App\Console\Commands\ImportLoveSongsLastFm;
use App\Http\Traits\LastFmDBTrait;
use App\Http\Traits\LastFmTrait;
use App\Models\LastFmArtist;
use App\Models\LastFmPeriodTime;
use App\Models\LastFmSong;
use App\Models\LastFmUser;
use Barryvanveen\Lastfm\Constants;
use Barryvanveen\Lastfm\Exceptions\InvalidPeriodException;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

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
        $this->setUsername(config('lastfm.user'));
    }

    /**
     * @param  LastFmPeriodTime|null  $lastFmPeriodTime
     * @return Collection
     */
    public function getWeeklyTrackChart(?LastFmPeriodTime $lastFmPeriodTime) : Collection
    {

        [$from, $to] = $this->getFromToPeriodTime($lastFmPeriodTime);
        $this->query = array_merge($this->query, [
            'method' => 'user.getweeklytrackchart',
            'user'   => $this->username,
            'from'   => $from,
            'to'     => $to,
        ]);
        $this->pluck = 'weeklytrackchart';
        $chart       = $this->getFullData();
        return collect(
            [
                'tracks' => $this->getWeeklyTrackChartFiltered($chart)
                                 ->toCollection()
            ]);

    }

    /**
     * @return Collection
     */
    public function getUserWeeklyChartList() : Collection
    {
        $this->query = array_merge($this->query, [
            'method' => 'user.getWeeklyChartList',
            'user'   => $this->username,
        ]);

        $this->pluck = 'weeklychartlist.chart';
        return $this->getFullData();
    }

    /**
     * @param  ImportAllChartWeeklyLastFm  $console
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getChartWeekly(ImportAllChartWeeklyLastFm $console) : void
    {
        $this->getUserWeeklyChartList()
             ->each(function (Collection $chartPeriod) use ($console) {
                 $periodTime = $this->getLastFmPeriodTime($chartPeriod);
                 session(['periodTime' => $periodTime]);

                 $this->reProcessPeriodStats($console, $periodTime);

                 $this->weeklyTrackChart($periodTime, $console);
             });;
    }

    /**
     * @param  ImportLoveSongsLastFm  $console
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getLovedTracks(ImportLoveSongsLastFm $console) : void
    {

        $this->getLovedTracksCollect()
             ->each(function (Collection $song) use ($console) {
                 $lastFmArtist = $this->getLastFmArtistFromDB($this->getLastFmArtistFromAPI($song));
                 $lastFmSong   = $this->getLastFmSong($song, $lastFmArtist);
                 $this->getLastFmLoveSong($lastFmSong, $song);
                 $this->updateLastFmSongInfo($lastFmSong);
                 $console->info("Artist: {$lastFmArtist->name}");
                 $console->warn("Song: {$song->get('name', '')}");
                 $console->newLine();
             });
    }

    /**
     * @param  LastFmArtist  $lastFmArtist
     * @return Collection
     */
    public function getArtistTags(LastFmArtist $lastFmArtist) : Collection
    {

        $lastFmArtist->tags()
                     ->sync([]);

        $this->query = array_merge($this->query, [
            'method' => 'artist.getTopTags',
            'artist' => $lastFmArtist->name,
            'user'   => $this->username,
        ]);
        $this->pluck = 'toptags.tag';

        return $this->getFullData()
                    ->filter(function (Collection $tag) {
                        return $tag->get('count') >= config('lastfm.top_tags_count');
                    });
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
     * @return Collection
     */
    public function getUserInfo() : Collection
    {
        return $this->userInfo($this->username)
                    ->getFullData();
    }


    /**
     * @param  string  $pluck
     * @return Collection
     */
    public function userLoveTracks(string $pluck = 'lovedtracks') : Collection
    {
        $this->query = array_merge($this->query, [
            'method'      => 'user.getLovedTracks',
            'autocorrect' => 1,
            'user'        => $this->username,
            'limit'       => config('lastfm.limit_loves'),
        ]);
        $this->pluck = $pluck;

        return $this->getFullData();
    }

    /**
     * @param  Carbon  $initDate
     * @param  int  $months
     * @return void
     */
    public function getUserTopTracksPeriodTimeByMonths(Carbon $initDate, int $months = 1) : void
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
            /*
            dd($this->weeklyTrackChart());
            $songs->push(
                $this->weeklyTrackChart()
                     ->filter(function ($song) {
                         return collect($song)->get('playcount', 0) >= 5 && collect($song)->get('playcount', 0) <= 10;
                     }));
            */

            $initDate->addSecond();
        }


        dd($songs->flatten(1));
    }

    /**
     * @param  LastFmArtist  $lastFmArtist
     * @return Collection
     */
    function getArtistInfo(LastFmArtist $lastFmArtist) : Collection
    {
        $this->query = array_merge($this->query, [
            'method'      => 'artist.getInfo',
            'artist'      => $lastFmArtist->name,
            'mbid'        => '',
            'autocorrect' => 1,
        ]);
        $this->pluck = 'artist';
        try {
            $artist = $this->getFullData();
            $artist->offsetUnset('similar');
            $artist->offsetUnset('bio');

            return $artist;
        } catch (Exception $e) {
            $this->pluck = null;
            dd($this->query, $this->getFullData(), $e->getMessage());
        }

    }

    /**
     * @return LastFmUser
     */
    public function getLastFmUser() : LastFmUser
    {
        return $this->lastFmUser;
    }

    /**
     * @param  LastFmSong  $lastFmSong
     * @return Collection
     */
    public function trackGetInfo(LastFmSong $lastFmSong) : Collection
    {
        $this->query = array_merge($this->query, [
            'method' => 'track.getInfo',
            'track'  => $lastFmSong->name,
            'artist' => $lastFmSong->artist->name,
            'mbid'   => '',
        ]);
        $this->pluck = 'track';

        try {
            $trackInfo = $this->getFullData();
            $trackInfo->offsetUnset('wiki');

            return $trackInfo;
        } catch (Exception $e) {
            return collect();
        }
    }

    /**
     * @return array|bool
     */
    protected function getNowListening() : bool|array
    {
        return parent::nowListening($this->username);
    }

}
