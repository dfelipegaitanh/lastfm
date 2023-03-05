<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\LastFmUser
 *
 * @property int $id
 * @property string $name
 * @property string $age
 * @property string $subscriber
 * @property string $realname
 * @property string $bootstrap
 * @property mixed $image
 * @property mixed $registered
 * @property string $country
 * @property string $gender
 * @property string $url
 * @property string $type
 * @property string|null $dateFirstScrobble
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, \App\Models\LastFmLoveSong> $songs
 * @property-read int|null $songs_count
 * @property-read Collection<int, \App\Models\LastFmUserStat> $stats
 * @property-read int|null $stats_count
 * @method static Builder|LastFmUser newModelQuery()
 * @method static Builder|LastFmUser newQuery()
 * @method static Builder|LastFmUser query()
 * @method static Builder|LastFmUser whereAge($value)
 * @method static Builder|LastFmUser whereBootstrap($value)
 * @method static Builder|LastFmUser whereCountry($value)
 * @method static Builder|LastFmUser whereCreatedAt($value)
 * @method static Builder|LastFmUser whereDateFirstScrobble($value)
 * @method static Builder|LastFmUser whereGender($value)
 * @method static Builder|LastFmUser whereId($value)
 * @method static Builder|LastFmUser whereImage($value)
 * @method static Builder|LastFmUser whereName($value)
 * @method static Builder|LastFmUser whereRealname($value)
 * @method static Builder|LastFmUser whereRegistered($value)
 * @method static Builder|LastFmUser whereSubscriber($value)
 * @method static Builder|LastFmUser whereType($value)
 * @method static Builder|LastFmUser whereUpdatedAt($value)
 * @method static Builder|LastFmUser whereUrl($value)
 * @mixin Eloquent
 */
class LastFmUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subscriber',
        'realname',
        'bootstrap',
        'image',
        'registered',
        'country',
        'gender',
        'url',
        'type',
    ];

    /**
     * @return HasMany
     */
    public function songs() : HasMany
    {
        return $this->hasMany(LastFmLoveSong::class);
    }

    /**
     * @return HasMany
     */
    public function stats() : HasMany
    {
        return $this->hasMany(LastFmUserStat::class);
    }
}
