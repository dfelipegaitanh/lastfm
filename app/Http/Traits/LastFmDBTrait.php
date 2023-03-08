<?php

namespace App\Http\Traits;

use App\Console\Commands\ImportAllChartWeeklyLastFm;
use App\Models\LastFmArtist;
use App\Models\LastFmArtistStat;
use App\Models\LastFmImageArtist;
use App\Models\LastFmLoveSong;
use App\Models\LastFmPeriodTime;
use App\Models\LastFmSong;
use App\Models\LastFmSongStat;
use App\Models\LastFmTag;
use App\Models\LastFmUser;
use App\Models\LastFmUserStat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

trait LastFmDBTrait
{

    /**
     * @param  Collection  $artist
     * @param  string  $artistKey
     * @return LastFmArtist
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function getLastFmArtistFromDB(Collection $artist, string $artistKey = 'name') : LastFmArtist
    {
        $lastFmArtist = LastFmArtist::firstOrNew(['name' => $artist->get($artistKey)]);
        if (is_null($lastFmArtist->id)) {
            $lastFmArtist->url  = $this->getKeyValue($artist, 'url', false);
            $lastFmArtist->mbid = $this->getKeyValue($artist, 'mbid', false);
            $lastFmArtist->save();
        }
        $this->updateLastFmArtistInfo($lastFmArtist);
        return $lastFmArtist;
    }

    /**
     * @param  Collection  $user
     * @return LastFmUser
     */
    public function getLastFmUser(Collection $user) : LastFmUser
    {
        $lastFmUser = $this->createLastFmUserInDB($user);
        $this->createLastUserStatInDB($user, $lastFmUser);
        return $lastFmUser;
    }

    /**
     * @param  Collection  $song
     * @param  LastFmArtist  $lastFmArtist
     * @return LastFmSong
     */
    function getLastFmSong(Collection $song, LastFmArtist $lastFmArtist) : LastFmSong
    {
        $lastFmSong = LastFmSong::firstOrNew(
            [
                'name'              => $song->get('name', ''),
                'last_fm_artist_id' => $lastFmArtist->id
            ]);

        $lastFmSong->url        = $song->get('url', '');
        $lastFmSong->image      = collect($song['image'] ?? [])->toJson();
        $lastFmSong->streamable = collect($song['streamable'] ?? [])->toJson();
        $lastFmSong->artist()->associate($lastFmArtist);
        $lastFmSong->save();
        return $lastFmSong;
    }

    /**
     * @param  LastFmSong  $lastFmSong
     * @param  Collection  $song
     * @return void
     */
    function getLastFmLoveSong(LastFmSong $lastFmSong, Collection $song) : void
    {
        /* FIXME: use attach? */
        $lastFmLoveSong       = LastFmLoveSong::firstOrNew(
            [
                'last_fm_song_id' => $lastFmSong->id,
                'last_fm_user_id' => $this->lastFmUser->id
            ]
        );
        $lastFmLoveSong->uts  = $this->getLoveSongUts($song);
        $lastFmLoveSong->date = $this->getLoveSongDate($song);
        $lastFmLoveSong->lastFmUser()->associate($this->lastFmUser);
        $lastFmLoveSong->lastFmSong()->associate($lastFmSong);
        $lastFmLoveSong->save();
    }

    /**
     * @param  Collection  $song
     * @return float
     */
    public function getLoveSongUts(Collection $song) : float
    {
        return (float) collect($song->get('date', []))->get('uts', '');
    }

    /**
     * @param  Collection  $song
     * @return Carbon
     */
    public function getLoveSongDate(Collection $song) : Carbon
    {
        return new Carbon(collect($song->get('date', []))
                              ->get('#text', ''));
    }


    /**
     * @param  Collection|array  $tag
     * @param  LastFmArtist|LastFmSong  $data
     * @return void
     */
    function createTagAssociation(Collection|array $tag, LastFmArtist|LastFmSong $data) : void
    {
        if (is_array($tag)) {
            $tag = collect($tag);
        }

        $data->tags()
             ->attach(LastFmTag::firstOrCreate(
                 [
                     'name' => $tag->get('name', ''),
                     'url'  => $tag->get('url', ''),
                 ])->id);
    }

    /**
     * @param  Collection  $user
     * @return LastFmUser
     */
    public function createLastFmUserInDB(Collection $user) : LastFmUser
    {
        $lastFmUser                    = LastFmUser::firstOrNew(['name' => $this->getUsername()]);
        $lastFmUser->age               = $this->getKeyValue($user, 'age');
        $lastFmUser->subscriber        = $this->getKeyValue($user, 'subscriber');
        $lastFmUser->realname          = $this->getKeyValue($user, 'realname');
        $lastFmUser->bootstrap         = $this->getKeyValue($user, 'bootstrap');
        $lastFmUser->image             = $user->get('image')->toJson() ?? '{}';
        $lastFmUser->registered        = $user->get('registered')->toJson() ?? '';
        $lastFmUser->country           = $this->getKeyValue($user, 'country');
        $lastFmUser->gender            = $this->getKeyValue($user, 'gender');
        $lastFmUser->url               = $this->getKeyValue($user, 'url');
        $lastFmUser->type              = $this->getKeyValue($user, 'type');
        $lastFmUser->dateFirstScrobble = Carbon::createFromTimestamp($this->getKeyValue($user, 'registered', true, 'unixtime'))
                                               ->format('Y-m-d H:i:s');
        $lastFmUser->save();
        return $lastFmUser;
    }

    /**
     * @param  Collection  $user
     * @return int[]
     */
    public function getLastFmUserStatsData(Collection $user) : array
    {
        return [
            'playcount'    => (int) $this->getKeyValue($user, 'playcount'),
            'artist_count' => (int) $this->getKeyValue($user, 'artist_count'),
            'playlists'    => (int) $this->getKeyValue($user, 'playlists'),
            'track_count'  => (int) $this->getKeyValue($user, 'track_count'),
            'album_count'  => (int) $this->getKeyValue($user, 'album_count'),
        ];
    }

    /**
     * @param  Collection  $user
     * @param  LastFmUser  $lastFmUser
     * @return void
     */
    public function createLastUserStatInDB(Collection $user, LastFmUser $lastFmUser) : void
    {
        LastFmUserStat::firstOrNew($this->getLastFmUserStatsData($user))
                      ->user()
                      ->associate($lastFmUser)
                      ->save();
    }

    /**
     * @param  LastFmArtist  $lastFmArtist
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function lastFmArtistInSession(LastFmArtist $lastFmArtist) : bool
    {
        return collect(session()->get('artists.processed'))->contains($lastFmArtist->id) === true;
    }

    /**
     * @param  Collection  $artistInfo
     * @return int
     */
    public function getArtistStatsUserPlayCount(Collection $artistInfo) : int
    {
        return $artistInfo->get('stats', collect())
                          ->get('userplaycount', 0);
    }

    /**
     * @param  Collection  $artistInfo
     * @param  LastFmArtist  $lastFmArtist
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function saveArtistStats(Collection $artistInfo, LastFmArtist $lastFmArtist) : void
    {
        LastFmArtistStat::firstOrCreate(
            [
                'userplaycount'          => $this->getArtistStatsUserPlayCount($artistInfo),
                'last_fm_artist_id'      => $lastFmArtist->id,
                'last_fm_user_id'        => $this->lastFmUser->id,
                'last_fm_period_time_id' => session()->get('periodTime')->id,
            ]
        );
    }

    /**
     * @param  Collection  $artistInfo
     * @param  LastFmArtist  $lastFmArtist
     * @return void
     */
    public function saveArtistImages(Collection $artistInfo, LastFmArtist $lastFmArtist) : void
    {
        LastFmImageArtist::where('last_fm_artist_id', $lastFmArtist->id)
                         ->update(['actual' => false]);
        $artistInfo->get('image', collect())
                   ->each(function ($image) use ($lastFmArtist) {
                       $image = LastFmImageArtist::firstOrCreate(
                           [
                               'last_fm_artist_id' => $lastFmArtist->id,
                               'image'             => $image['#text'],
                               'size'              => $image['size'],
                           ]);

                       $image->actual = true;
                       $image->save();
                   });
    }

    /**
     * @param  Collection  $data
     * @param  string  $key
     * @return Collection
     */
    public function getInfoTags(Collection $data, $key = "tags") : Collection
    {
        return collect($data->get($key, collect())->get('tag', []));
    }

    /**
     * @param  Collection  $artistInfo
     * @param  LastFmArtist  $lastFmArtist
     * @return void
     */
    public function saveArtistsTag(Collection $artistInfo, LastFmArtist $lastFmArtist) : void
    {
        $lastFmArtist->tags()->sync([]);
        $this->getInfoTags($artistInfo)
             ->each(function ($tag) use ($lastFmArtist) {
                 $this->createTagAssociation($tag, $lastFmArtist);
             });
    }

    /**
     * @param  LastFmArtist  $lastFmArtist
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function updateLastFmArtistInfo(LastFmArtist $lastFmArtist) : void
    {
        if (!$this->lastFmArtistInSession($lastFmArtist)) {
            session()->push('artists.processed', $lastFmArtist->id);
            $artistInfo = $this->getArtistInfo($lastFmArtist);
            $this->saveArtistImages($artistInfo, $lastFmArtist);
            $this->saveArtistStats($artistInfo, $lastFmArtist);
            $this->saveArtistsTag($artistInfo, $lastFmArtist);
        }
    }

    /**
     * @param  LastFmSong  $lastFmSong
     * @param  Collection|null  $song
     * @return Collection
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function updateLastFmSongInfo(LastFmSong $lastFmSong, ?Collection $song = null) : Collection
    {
        $trackInfo = $this->trackGetInfo($lastFmSong);
        if ($trackInfo->isNotEmpty()) {
            $this->saveSongStats($trackInfo, $lastFmSong, $song);
            $this->saveSongTags($lastFmSong, $trackInfo);
        }
        return $trackInfo;
    }

    /**
     * @param  Collection  $trackInfo
     * @param  LastFmSong  $lastFmSong
     * @param  Collection|null  $song
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function saveSongStats(Collection $trackInfo, LastFmSong $lastFmSong, ?Collection $song) : void
    {

        LastFmSongStat::firstOrCreate(
            [
                'userplaycount'          => $this->getSongStatsUserPlayCount($song, $trackInfo),
                'last_fm_song_id'        => $lastFmSong->id,
                'last_fm_user_id'        => $this->lastFmUser->id,
                'last_fm_period_time_id' => session()->get('periodTime')->id,
            ]
        );
    }

    /**
     * @param  LastFmSong  $lastFmSong
     * @param  Collection  $trackInfo
     * @return void
     */
    public function saveSongTags(LastFmSong $lastFmSong, Collection $trackInfo) : void
    {
        $lastFmSong->tags()->sync([]);
        $this->getInfoTags($trackInfo, 'toptags')
             ->each(function ($tag) use ($lastFmSong) {
                 $this->createTagAssociation($tag, $lastFmSong);
             });
    }


    /**
     * @param  Collection  $chartPeriod
     * @return LastFmPeriodTime
     */
    function getLastFmPeriodTime(Collection $chartPeriod) : LastFmPeriodTime
    {
        $periodTime = LastFmPeriodTime::firstOrNew(
            [
                'dateStart' => $this->getPeriodTimeDateStart($chartPeriod),
                'dateEnd'   => $this->getPeriodTimeDateEnd($chartPeriod),
            ]
        );

        $periodTime->type = 'weekly';
        $periodTime->save();
        return $periodTime;
    }

    /**
     * @param  Collection  $chartPeriod
     * @return string
     */
    function getPeriodTimeDateStart(Collection $chartPeriod) : string
    {
        return (new Carbon((int) $chartPeriod->get('from')))
            ->format($this->dateFormat());
    }

    /**
     * @param  Collection  $chartPeriod
     * @return string
     */
    function getPeriodTimeDateEnd(Collection $chartPeriod) : string
    {
        return (new Carbon((int) $chartPeriod->get('to')))
            ->format($this->dateFormat());
    }

    /**
     * @param  Collection|null  $song
     * @param  Collection  $trackInfo
     * @return \Closure|int|mixed|string
     */
    public function getSongStatsUserPlayCount(?Collection $song, Collection $trackInfo) : mixed
    {
        return !is_null($song)
            ? $song->get('playcount', 0)
            : $this->getKeyValue($trackInfo, 'userplaycount');
    }

    /**
     * @param  LastFmPeriodTime  $periodTime
     * @param  bool  $isCompleted
     * @return void
     */
    function updatePeriodTimeIsCompleted(LastFmPeriodTime $periodTime, bool $isCompleted = true) : void
    {
        $periodTime->is_completed = $isCompleted;
        $periodTime->save();
    }

    /**
     * @param  LastFmPeriodTime  $periodTime
     * @param  Collection  $songs
     * @param  ImportAllChartWeeklyLastFm  $console
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function processWeeklyTrackChart(LastFmPeriodTime $periodTime, Collection $songs, ImportAllChartWeeklyLastFm $console) : void
    {
        $console->alert('Period ID: '.$periodTime->id.'. From '.$periodTime->dateStart.' To '.$periodTime->dateEnd);
        if ($songs->isNotEmpty()) {

            $this->reProcessPeriodStats($console, $periodTime);

            $console->info('This period have '.$songs->count().' songs');
            $this->createOrUpdateSongsData($songs, $console);

            $console->table(
                ['Artist', 'Song', 'Count'],
                $periodTime->songsWithArtis()->toArray()
            );
        }
        else {
            $console->error('This period doesn\'t have any songs');
        }

        $this->updatePeriodTimeIsCompleted($periodTime);
    }

    /**
     * @param  Collection  $songs
     * @param  ImportAllChartWeeklyLastFm  $console
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function createOrUpdateSongsData(Collection $songs, ImportAllChartWeeklyLastFm $console) : void
    {
        $bar = $console->getOutput()->createProgressBar($songs->count());
        $bar->start();
        $songs->each(function (Collection $song) use ($console, $bar) {
            $lastFmArtist = $this->getLastFmArtistFromDB($this->getLastFmArtistFromAPI($song), "#text");
            $lastFmSong   = $this->getLastFmSong($song, $lastFmArtist);
            $this->updateLastFmSongInfo($lastFmSong, $song);
            $bar->advance();
        });
        $bar->finish();
        $console->newLine();
    }

    /**
     * @param  ImportAllChartWeeklyLastFm  $console
     * @param  LastFmPeriodTime  $periodTime
     * @return void
     */
    public function reProcessPeriodStats(ImportAllChartWeeklyLastFm $console, LastFmPeriodTime $periodTime) : void
    {
        if ((int) $console->option('reProcess') === 1) {
            $periodTime->songsStats()
                       ->delete();
            $periodTime->artistStats()
                       ->delete();
        }
    }

}
