<?php

namespace App\Http\Traits;

use App\Models\LastFmPeriodTime;
use App\Models\LastFmUser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

trait LastFmTrait
{

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
        return collect($this->get())
            ->toCollection();
    }

    /**
     * @param  Collection  $data
     * @param  string  $key
     * @param  bool  $array
     * @param  string  $position
     * @return \Closure|mixed|string
     */
    public function getKeyValue(Collection $data, string $key, bool $array = true, string $position = "0") : mixed
    {
        return ($array === true)
            ? ($data->get($key)[$position] ?? '')
            : ($data->get($key) ?? '');
    }

    /**
     * @param  string  $username
     */
    public function setUsername(string $username) : void
    {
        $this->username = $username;
    }

    /**
     * @param  LastFmUser  $lastFmUser
     */
    public function setLastFmUser(LastFmUser $lastFmUser) : void
    {
        $this->lastFmUser = $lastFmUser;
    }

    /**
     * @return Collection
     */
    public function getLovedTracksCollect() : Collection
    {
        $loveTracks = $this->userLoveTracks();
        $attr       = $this->getAttr($loveTracks);
        $songs      = collect($loveTracks->get('track', []))->toCollection();
        $this->attachAllLovedTracks($attr, $songs);
        return $songs;
    }


    /**
     * @param  Collection  $song
     * @return Collection
     */
    function getLastFmArtistFromAPI(Collection $song) : Collection
    {
        return collect($song->get("artist", []));
    }

    /**
     * @param  Collection  $attr
     * @param $songs
     * @return void
     */
    protected function attachAllLovedTracks(Collection $attr, $songs) : void
    {
        for ($i = $attr->get('page', 0) + 1; $i <= $attr->get('totalPages', 0); $i++) {
            $this->page($i);
            $this->userLoveTracks('lovedtracks.track')
                 ->each(function (Collection $song) use ($songs) {
                     $songs->push($song);
                 });
        }
    }

    /**
     * @return string
     */
    public function dateFormat() : string
    {
        return 'Y-m-d H:i:s';
    }

    /**
     * @param  LastFmPeriodTime|null  $lastFmPeriodTime
     * @return array
     */
    public function getFromToPeriodTime(?LastFmPeriodTime $lastFmPeriodTime) : array
    {
        if (is_null($lastFmPeriodTime)) {
            $from = Carbon::today()
                          ->addHours(5)
                          ->subWeek();
            $to   = $from->addWeek()
                         ->format('U');
            $from = $from->subWeek()
                         ->format('U');
        }
        else {
            $from = (new Carbon($lastFmPeriodTime->dateStart))->format('U');
            $to   = (new Carbon($lastFmPeriodTime->dateEnd))->format('U');
        }
        return [$from, $to];
    }

}
