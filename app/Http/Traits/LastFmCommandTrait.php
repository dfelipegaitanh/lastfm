<?php

namespace App\Http\Traits;

use App\Http\Classes\LastFm;
use App\Models\LastFmUser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

trait LastFmCommandTrait
{

    use DateTraits;

    /**
     * @return string
     */
    public function getUsername() : mixed
    {
        return is_null($this->argument('user')) ? config('lastfm.user') : $this->argument('user');
    }

    /**
     * @return Collection
     */
    public function getDates() : Collection
    {
        $initDate = $this->getInitDate();
        return collect([
                           'initDate' => $initDate,
                           'endDate'  => $this->getEndDate($initDate)
                       ]);
    }


    /**
     * @param  LastFm  $lastFm
     * @return void
     */
    public function setUpLastFm(LastFm &$lastFm) : void
    {
        $lastFm->setUsername($this->getUsername());
        $lastFm->setDates($this->getDates());
        $lastFm->setLimit($this->option('limit'));
        $lastFm->setMinPlays($this->option('minPlays'));
    }

    public function setUpLastFmLoveSongs(LastFm &$lastFm) : void
    {
        $lastFm->setUsername($this->getUsername());
        $user = $lastFm->getUserInfo();
        $lastFm->setLastFmUser($this->getLastFmUser($user));
    }

    /**
     * @return false|Carbon
     */
    public function getInitDate() : Carbon|false
    {
        return Carbon::create(
            ($this->option('initYear') ?? config('lastfm.init_year')),
            $this->validMonth($this->option('initMonth')) ?? 1);
    }

    /**
     * @param  Carbon  $initDate
     * @return Carbon
     */
    public function getEndDate(Carbon $initDate) : Carbon
    {
        $endDate = Carbon::create(
            ($this->option('endYear') ?? config('lastfm.end_year')),
            $this->validMonth($this->option('endMonth'), true) ?? 12);

        if ($initDate->greaterThan($endDate)) {
            $endDate = $initDate;
        }

        return $endDate;
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
     * @return \Closure|mixed|string
     */
    public function getKeyValue(Collection $user, $key) : mixed
    {
        return $user->get($key)[0] ?? '';
    }

}
