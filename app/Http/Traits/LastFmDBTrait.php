<?php

namespace App\Http\Traits;

use App\Models\LastFmArtist;
use App\Models\LastFmLoveSong;
use App\Models\LastFmSong;
use App\Models\LastFmTag;
use App\Models\LastFmUser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

trait LastFmDBTrait
{

    /**
     * @param  Collection  $artist
     * @return LastFmArtist
     */
    function getLastFmArtist(Collection $artist) : LastFmArtist
    {
        $lastFmArtist       = LastFmArtist::firstOrNew(['name' => $artist->get('name')]);
        $lastFmArtist->url  = $this->getKeyValue($artist, 'age', false);
        $lastFmArtist->mbid = $this->getKeyValue($artist, 'mbid', false);
        $lastFmArtist->save();
        return $lastFmArtist;
    }

    /**
     * @param  Collection  $user
     * @return LastFmUser
     */
    public function getLastFmUser(Collection $user) : LastFmUser
    {
        $lastFmUser               = LastFmUser::firstOrNew(['name' => $this->getUsername()]);
        $lastFmUser->age          = $this->getKeyValue($user, 'age');
        $lastFmUser->subscriber   = $this->getKeyValue($user, 'subscriber');
        $lastFmUser->realname     = $this->getKeyValue($user, 'realname');
        $lastFmUser->bootstrap    = $this->getKeyValue($user, 'bootstrap');
        $lastFmUser->playcount    = (int) $this->getKeyValue($user, 'playcount');
        $lastFmUser->artist_count = (int) $this->getKeyValue($user, 'artist_count');
        $lastFmUser->playlists    = (int) $this->getKeyValue($user, 'playlists');
        $lastFmUser->track_count  = (int) $this->getKeyValue($user, 'track_count');
        $lastFmUser->album_count  = (int) $this->getKeyValue($user, 'album_count');
        $lastFmUser->image        = $user->get('image')->toJson() ?? '{}';
        $lastFmUser->registered   = $user->get('registered')->toJson() ?? '';
        $lastFmUser->country      = $this->getKeyValue($user, 'country');
        $lastFmUser->gender       = $this->getKeyValue($user, 'gender');
        $lastFmUser->url          = $this->getKeyValue($user, 'url');
        $lastFmUser->type         = $this->getKeyValue($user, 'type');
        $lastFmUser->save();
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

        $lastFmArtist->lastFmTags()
                     ->attach($lastFmTag->id, ['count' => $tag->get('count', 0)]);
    }

}
