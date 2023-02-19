<?php

namespace App\Http\Classes;

use GuzzleHttp\Client;

class LastFm extends \Barryvanveen\Lastfm\Lastfm
{

    private string $user;

    public function __construct(Client $client)
    {
        $this->user = config('lastfm.user');
        parent::__construct($client, config('lastfm.api_key')) ;
    }

}
