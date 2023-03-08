<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;

/**
 * App\Models\LastFmPeriodTime
 *
 * @property int $id
 * @property string|null $dateStart
 * @property string|null $dateEnd
 * @property string $type
 * @property bool $is_completed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, \App\Models\LastFmArtistStat> $artistStats
 * @property-read int|null $artist_stats_count
 * @property-read Collection<int, \App\Models\LastFmSong> $songs
 * @property-read int|null $songs_count
 * @property-read Collection<int, \App\Models\LastFmSongStat> $songsStats
 * @property-read int|null $songs_stats_count
 * @method static Builder|LastFmPeriodTime newModelQuery()
 * @method static Builder|LastFmPeriodTime newQuery()
 * @method static Builder|LastFmPeriodTime query()
 * @method static Builder|LastFmPeriodTime whereCreatedAt($value)
 * @method static Builder|LastFmPeriodTime whereDateEnd($value)
 * @method static Builder|LastFmPeriodTime whereDateStart($value)
 * @method static Builder|LastFmPeriodTime whereId($value)
 * @method static Builder|LastFmPeriodTime whereIsCompleted($value)
 * @method static Builder|LastFmPeriodTime whereType($value)
 * @method static Builder|LastFmPeriodTime whereUpdatedAt($value)
 * @mixin Eloquent
 */
class LastFmPeriodTime extends Model
{
    use HasFactory;

    protected $casts = [
        'is_completed' => 'boolean'
    ];

    protected $fillable = [
        'dateStart',
        'dateEnd',
    ];

    /**
     * @return HasMany
     */
    public function artistStats() : HasMany
    {
        return $this->hasMany(LastFmArtistStat::class);
    }

    /**
     * @return HasManyThrough
     */
    public function songs() : HasManyThrough
    {
        return $this->hasManyThrough(
            LastFmSong::class,
            LastFmSongStat::class,
            'last_fm_period_time_id',
            'id',
            null,
            'last_fm_song_id',
        );
    }

    /**
     * @return Collection|\Illuminate\Support\Collection
     */
    public function songsWithArtis() : Collection|\Illuminate\Support\Collection
    {
        return $this->songs
            ->map(function (LastFmSong $song) {
                return [$song->artist->name, $song->name];
            });
    }

    /**
     * @return HasMany
     */
    public function songsStats() : HasMany
    {
        return $this->hasMany(LastFmSongStat::class);
    }

}
