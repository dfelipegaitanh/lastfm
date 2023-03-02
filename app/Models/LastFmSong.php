<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LastFmSong
 *
 * @property int $id
 * @property int $last_fm_artist_id
 * @property string $mbid
 * @property string $name
 * @property string $url
 * @property mixed $image
 * @property mixed $streamable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSong newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSong newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSong query()
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSong whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSong whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSong whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSong whereLastFmArtistId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSong whereMbid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSong whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSong whereStreamable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSong whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSong whereUrl($value)
 * @property-read \App\Models\LastFmArtist|null $lastFmArtist
 * @mixin \Eloquent
 */
class LastFmSong extends Model
{
    use HasFactory;

    protected $fillable = [
        'mbid',
        'name',
        'url',
        'image',
        'streamable',
    ];

    public function lastFmArtist()
    {
        return $this->belongsTo(LastFmArtist::class);
    }

}
