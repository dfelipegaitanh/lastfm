<?php

namespace App\Http\Traits;

use App\Models\LastFmArtist;
use App\Models\LastFmLoveSong;
use App\Models\LastFmSong;
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
        if (!$this->lastFmArtistInSession($lastFmArtist)) {
            session()->push('processed.artists', $lastFmArtist->id);
            dd($this->lastFmUser, $this->getArtistInfo($artist));
        }
        dd(123);
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
                'mbid'              => $song->get('mbid', ''),
                'name'              => $song->get('name', ''),
                'last_fm_artist_id' => $lastFmArtist->id
            ]);

        $lastFmSong->url        = $song->get('url', '');
        $lastFmSong->image      = collect($song['image'] ?? [])->toJson();
        $lastFmSong->streamable = collect($song['streamable'] ?? [])->toJson();
        $lastFmSong->lastFmArtist()->associate($lastFmArtist);
        $lastFmSong->save();
        return $lastFmSong;
    }

    /**
     * @param  LastFmSong  $lastFmSong
     * @param  Collection  $song
     * @return LastFmLoveSong
     */
    function getLastFmLoveSong(LastFmSong $lastFmSong, Collection $song) : LastFmLoveSong
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
        return $lastFmLoveSong;
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
     * @param  Collection  $tag
     * @param  LastFmArtist  $lastFmArtist
     * @return void
     */
    function createArtistTag(Collection $tag, LastFmArtist $lastFmArtist) : void
    {
        $lastFmTag = LastFmTag::firstOrCreate(
            [
                'name' => $tag->get('name', ''),
                'url'  => $tag->get('url', ''),
            ]);

        $lastFmArtist->tags()
                     ->attach($lastFmTag->id, ['count' => $tag->get('count', 0)]);
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
        return collect(session()->get('processed.artists'))->contains($lastFmArtist->id) === true;
    }

}
