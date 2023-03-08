<?php

use Illuminate\Support\Carbon;

return [

    /*
     * You can get your own last.fm API key at http://www.last.fm/api/account/create.
     */
    'api_key'        => env('LASTFM_API_KEY'),
    'user'           => env('LASTFM_USER', ''),
    'limit'          => env('LASTFM_LIMIT', 10),
    'min_plays'      => env('LASTFM_MIN_PLAYS', 30),
    'min_plays_week' => env('LASTFM_MIN_PLAYS_WEEK', 5),
    'init_year'      => env('LASTFM_INIT_YEAR', 2006),
    'limit_loves'    => env('LASTFM_LIMIT_LOVE', 100),
    'top_tags_count' => env('LASTFM_TOP_TAGS_COUNT_FILTER', 30),
    'end_year'       => (new Carbon())->year,
    'diff_hours'     => env('LASTFM_DIFF_HOURS', 19),

];
