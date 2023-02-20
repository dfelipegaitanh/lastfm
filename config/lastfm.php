<?php

return [

    /*
     * You can get your own last.fm API key at http://www.last.fm/api/account/create.
     */
    'api_key'   => env('LASTFM_API_KEY') ,
    'user'      => env('LASTFM_USER' , '') ,
    'limit'     => env('LASTFM_LIMIT' , 10) ,
    'min_plays' => env('LASTFM_MIN_PLAYS' , 30) ,

];
