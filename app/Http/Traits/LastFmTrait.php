<?php

namespace App\Http\Traits;

use App\Models\LastFmUser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

trait LastFmTrait
{

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
     * @param  Collection  $data
     * @return Collection
     */
    public function getAttr(Collection $data) : Collection
    {
        return $data->get('@attr', collect());
    }

    /**
     * @return Collection
     */
    public function getData() : Collection
    {
        return $this->getFullData()
                    ->minPlays($this->min_plays);
    }

    /**
     * @return Collection
     */
    public function getFullData() : Collection
    {
        return collect(parent::get())
            ->toCollection();
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
     * @param  Collection  $user
     * @param $key
     * @param  bool  $array
     * @return \Closure|mixed|string
     */
    public function getKeyValue(Collection $user, $key, bool $array = true) : mixed
    {
        return ($array === true)
            ? ($user->get($key)[0] ?? '')
            : ($user->get($key) ?? '');
    }


    /**
     * @return Collection
     */
    public function getLovedTracksCollect() : Collection
    {
        $loveTracks = $this->userLoveTracks();

        $attr  = $this->getAttr($loveTracks);
        $songs = collect($loveTracks->get('track', []));

        for ($i = $attr->get('page', 0) + 1; $i <= $attr->get('totalPages', 0); $i++) {
            $this->page($i);
            $this->userLoveTracks('lovedtracks.track')
                 ->each(function (Collection $song) use ($songs) {
                     $songs->push($song->toArray());
                 });
        }
        return $songs;
    }


    /**
     * @param  array  $song
     * @return Collection
     */
    function getLastFmArtistFromAPI(array $song = []) : Collection
    {
        return collect(
            collect($song)
                ->get("artist", [])
        );
    }

}
