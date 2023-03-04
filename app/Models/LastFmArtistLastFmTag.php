<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LastFmArtistLastFmTag
 *
 * @property int $last_fm_artist_id
 * @property int $last_fm_tag_id
 * @property int $count
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmArtistLastFmTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmArtistLastFmTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmArtistLastFmTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmArtistLastFmTag whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmArtistLastFmTag whereLastFmArtistId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmArtistLastFmTag whereLastFmTagId($value)
 * @mixin \Eloquent
 */
class LastFmArtistLastFmTag extends Model
{
    use HasFactory;

    protected $table = "last_fm_artist_last_fm_tag";
}
