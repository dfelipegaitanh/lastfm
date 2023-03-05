<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\LastFmArtist $lastFmArtist
 * @property-read Collection<int, \App\Models\LastFmLoveSong> $lastFmLoveSong
 * @property-read int|null $last_fm_love_song_count
 * @method static Builder|LastFmSong newModelQuery()
 * @method static Builder|LastFmSong newQuery()
 * @method static Builder|LastFmSong query()
 * @method static Builder|LastFmSong whereCreatedAt($value)
 * @method static Builder|LastFmSong whereId($value)
 * @method static Builder|LastFmSong whereImage($value)
 * @method static Builder|LastFmSong whereLastFmArtistId($value)
 * @method static Builder|LastFmSong whereMbid($value)
 * @method static Builder|LastFmSong whereName($value)
 * @method static Builder|LastFmSong whereStreamable($value)
 * @method static Builder|LastFmSong whereUpdatedAt($value)
 * @method static Builder|LastFmSong whereUrl($value)
 * @mixin Eloquent
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

    public function lastFmArtist() : BelongsTo
    {
        return $this->belongsTo(LastFmArtist::class);
    }

    /**
     * @return HasMany
     */
    public function lastFmLoveSong() : HasMany
    {
        return $this->hasMany(LastFmLoveSong::class);
    }

}
