<?php

namespace App\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class LastFmProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register() : void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot() : void
    {
        Collection::macro('toCollection', function () {
            return $this->map(function ($value) {
                return collect($value);
            });
        });

        Collection::macro('minPlays', function (int $min_plays) {
            return $this->filter(function (Collection $song) use ($min_plays) {
                return $song->get('playcount', 0) >= $min_plays;
            });
        });


    }
}
