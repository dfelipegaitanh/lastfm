<?php

namespace App\Http\Classes;

use App\Console\Commands\ImportAllChartWeeklyLastFm;
use App\Console\Commands\ImportLoveSongsLastFm;
use App\Http\Traits\LastFmDBTrait;
use App\Http\Traits\LastFmTrait;
use App\Models\LastFmArtist;
use App\Models\LastFmPeriodTime;
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
        return collect(['tracks' => $this->getWeeklyTrackChartFiltered($chart)]);

    }

    /**
     * @return Collection
     */
    public function getUserWeeklyChartList(ImportAllChartWeeklyLastFm $console) : Collection
    {
        dd($console);
        $this->query = array_merge($this->query, [
            'method' => 'user.getWeeklyChartList',
            'user'   => $this->username,
        ]);

        $this->pluck = 'weeklychartlist.chart';
        $this->getFullData()
             ->each(function (Collection $chartPeriod) {

                 $periodTime = LastFmPeriodTime::firstOrNew(
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
                $this->updateLastFmSongInfo($song, $lastFmSong);
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
     * @param  Collection  $data
     * @return Collection
     */
    function getArtistInfo(Collection $data) : Collection
    {
        $this->query = array_merge($this->query, [
            'method'      => 'artist.getInfo',
            'artist'      => $data->get('name', ''),
            'mbid'        => '',
            'autocorrect' => 1,
        ]);
        $this->pluck = 'artist';
        try {
            $artist = $this->getFullData();
            $artist->offsetUnset('similar');
            $artist->offsetUnset('bio');

            return $artist;
        } catch (Exception) {
            $this->pluck = null;
            dd($this->query, $this->getFullData());
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
     * @param  Collection  $data
     * @return Collection
     */
    public function trackGetInfo(Collection $data) : Collection
    {
        $this->query = array_merge($this->query, [
            'method' => 'track.getInfo',
            'track'  => $data->get('name', ''),
            'artist' => collect($data->get('artist', []))->get('name', ''),
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
