<?php

namespace App\Http\Traits;

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
     * @return \Closure|mixed|string
     */
    public function getKeyValue(Collection $data, string $key, bool $array = true) : mixed
    {
        return ($array === true)
            ? ($data->get($key)[0] ?? '')
            : ($data->get($key) ?? '');
    }

    /**
     * @return Collection
     */
    public function getLovedTracksCollect() : Collection
    {
        $loveTracks = $this->userLoveTracks();

        $attr  = $this->getAttr($loveTracks);
        $songs = collect($loveTracks->get('track', []))->toCollection();

        for ($i = $attr->get('page', 0) + 1; $i <= $attr->get('totalPages', 0); $i++) {
            $this->page($i);
            $this->userLoveTracks('lovedtracks.track')
                 ->each(function (Collection $song) use ($songs) {
                     $songs->push($song);
                 });
        }
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

}
