<?php

namespace App\Http\Traits;

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
     * @return Collection
     */
    public function getAttr() : Collection
    {
        return $this->getFullData()
                    ->get('@attr', collect());
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
}
