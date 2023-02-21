<?php

return [

    /*
     * You can get your own last.fm API key at http://www.last.fm/api/account/create.
     */
    'api_key'    => env('LASTFM_API_KEY') ,
    'user'       => env('LASTFM_USER' , '') ,
    'limit'      => env('LASTFM_LIMIT' , 10) ,
    'min_plays'  => env('LASTFM_MIN_PLAYS' , 30) ,
    'init_year'  => env('LASTFM_INIT_YEAR' , 2006) ,
    'init_month' => env('LASTFM_INIT_MONTH' , 4) ,
    'end_year'   => (new \Illuminate\Support\Carbon())->year ,
    'end_month'  => (new \Illuminate\Support\Carbon())->month ,

];
