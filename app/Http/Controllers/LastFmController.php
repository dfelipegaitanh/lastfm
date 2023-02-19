<?php

namespace App\Http\Controllers;

use App\Http\Classes\LastFm;
use Illuminate\Http\Request;

class LastFmController extends Controller
{

    public function index(LastFm $lastFm){
        dd($lastFm);
    }

}
