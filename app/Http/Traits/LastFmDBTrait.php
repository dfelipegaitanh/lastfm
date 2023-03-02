<?php

namespace App\Http\Traits;

use App\Models\LastFmArtist;
use App\Models\LastFmSong;
use App\Models\LastFmUser;
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

}
