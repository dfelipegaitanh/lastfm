<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LastFmSongLastFmTag
 *
 * @property int $last_fm_song_id
 * @property int $last_fm_tag_id
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSongLastFmTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSongLastFmTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSongLastFmTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSongLastFmTag whereLastFmSongId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSongLastFmTag whereLastFmTagId($value)
 * @mixin \Eloquent
 */
class LastFmSongLastFmTag extends Model
{

    protected $table = "last_fm_song_last_fm_tag";

    use HasFactory;
}
