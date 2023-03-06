<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LastFmSongLastFmTag
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSongLastFmTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSongLastFmTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSongLastFmTag query()
 * @mixin \Eloquent
 */
class LastFmSongLastFmTag extends Model
{

    protected $table = "last_fm_song_last_fm_tag";

    use HasFactory;
}
