<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\LastFmArtist
 *
 * @property int $id
 * @property string $mbid
 * @property string $name
 * @property string $url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, \App\Models\LastFmImageArtist> $images
 * @property-read int|null $images_count
 * @property-read Collection<int, \App\Models\LastFmSong> $songs
 * @property-read int|null $songs_count
 * @property-read Collection<int, \App\Models\LastFmArtistStat> $stats
 * @property-read int|null $stats_count
 * @property-read Collection<int, \App\Models\LastFmTag> $tags
 * @property-read int|null $tags_count
 * @method static Builder|LastFmArtist newModelQuery()
 * @method static Builder|LastFmArtist newQuery()
 * @method static Builder|LastFmArtist query()
 * @method static Builder|LastFmArtist whereCreatedAt($value)
 * @method static Builder|LastFmArtist whereId($value)
 * @method static Builder|LastFmArtist whereMbid($value)
 * @method static Builder|LastFmArtist whereName($value)
 * @method static Builder|LastFmArtist whereUpdatedAt($value)
 * @method static Builder|LastFmArtist whereUrl($value)
 * @mixin Eloquent
 */
class LastFmArtist extends Model
{
    use HasFactory;

    protected $fillable = [
        'mbid',
        'name',
        'url',
    ];

    /**
     * @return HasMany
     */
    public function images() : HasMany
    {
        return $this->hasMany(LastFmImageArtist::class);
    }

    /**
     * @return HasMany
     */
    public function stats() : HasMany
    {
        return $this->hasMany(LastFmArtistStat::class);
    }

    /**
     * @return HasMany
     */
    public function songs() : HasMany
    {
        return $this->hasMany(LastFmSong::class);
    }

    /**
     * @return BelongsToMany
     */
    public function tags() : BelongsToMany
    {
        return $this->belongsToMany(LastFmTag::class);
    }

}
