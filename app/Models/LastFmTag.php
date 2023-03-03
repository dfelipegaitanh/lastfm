<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Tag
 *
 * @property int $id
 * @property string $name
 * @property string $url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LastFmArtist> $lastFmArtists
 * @property-read int|null $last_fm_artists_count
 * @method static Builder|LastFmTag newModelQuery()
 * @method static Builder|LastFmTag newQuery()
 * @method static Builder|LastFmTag query()
 * @method static Builder|LastFmTag whereCreatedAt($value)
 * @method static Builder|LastFmTag whereId($value)
 * @method static Builder|LastFmTag whereName($value)
 * @method static Builder|LastFmTag whereUpdatedAt($value)
 * @method static Builder|LastFmTag whereUrl($value)
 * @mixin Eloquent
 */
class LastFmTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
    ];

    /**
     * @return BelongsToMany
     */
    public function lastFmArtists() : BelongsToMany
    {
        return $this->belongsToMany(LastFmArtist::class);
    }
}
