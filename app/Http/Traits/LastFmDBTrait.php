<?php

namespace App\Http\Traits;

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
     * @return LastFmArtist
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function getLastFmArtistFromDB(Collection $artist) : LastFmArtist
    {
        $lastFmArtist       = LastFmArtist::firstOrNew(['name' => $artist->get('name')]);
        $lastFmArtist->url  = $this->getKeyValue($artist, 'url', false);
        $lastFmArtist->mbid = $this->getKeyValue($artist, 'mbid', false);
        $lastFmArtist->save();
        $this->updateLastFmArtistInfo($lastFmArtist, $artist);
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

        $artistStats = LastFmArtistStat::firstOrNew(
            [
                'userplaycount'     => $this->getArtistStatsUserPlayCount($artistInfo),
                'last_fm_artist_id' => $lastFmArtist->id,
                'last_fm_user_id'   => $this->lastFmUser->id,
            ]
        );
        $artistStats->periodTime()->associate(session()->get('periodTime'));
        $artistStats->save();
    }

    /**
     * @param  Collection  $artistInfo
     * @return void
     */
    public function saveArtistImages(Collection $artistInfo) : void
    {
        LastFmImageArtist::where('last_fm_artist_id', $this->lastFmUser->id)
                         ->update(['actual' => false]);
        $artistInfo->get('image', collect())
                   ->each(function ($image) {
                       $image = LastFmImageArtist::firstOrCreate(
                           [
                               'last_fm_artist_id' => $this->lastFmUser->id,
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
     * @param  Collection  $artist
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function updateLastFmArtistInfo(LastFmArtist $lastFmArtist, Collection $artist) : void
    {
        if (!$this->lastFmArtistInSession($lastFmArtist)) {
            session()->push('artists.processed', $lastFmArtist->id);
            $artistInfo = $this->getArtistInfo($artist);
            $this->saveArtistImages($artistInfo);
            $this->saveArtistStats($artistInfo, $lastFmArtist);
            $this->saveArtistsTag($artistInfo, $lastFmArtist);
        }
    }

    /**
     * @param  Collection  $song
     * @param  LastFmSong  $lastFmSong
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function updateLastFmSongInfo(Collection $song, LastFmSong $lastFmSong) : void
    {
        $trackInfo = $this->trackGetInfo($song);
        if ($trackInfo->isNotEmpty()) {
            $this->saveSongStats($trackInfo, $lastFmSong);
            $this->saveSongTags($lastFmSong, $trackInfo);
        }
    }

    /**
     * @param  Collection  $trackInfo
     * @param  LastFmSong  $lastFmSong
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function saveSongStats(Collection $trackInfo, LastFmSong $lastFmSong) : void
    {
        $songStats = LastFmSongStat::firstOrNew(
            [
                'userplaycount'   => $this->getKeyValue($trackInfo, 'userplaycount'),
                'last_fm_song_id' => $lastFmSong->id,
                'last_fm_user_id' => $this->lastFmUser->id,
            ]
        );
        $songStats->periodTime()->associate(session()->get('periodTime'));
        $songStats->save();
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

}
