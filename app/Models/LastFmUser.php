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
 * @property int $playcount
 * @property int $artist_count
 * @property int $playlists
 * @property int $track_count
 * @property int $album_count
 * @property mixed $image
 * @property mixed $registered
 * @property string $country
 * @property string $gender
 * @property string $url
 * @property string $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, LastFmLoveSong> $lastFmLoveSong
 * @property-read int|null $last_fm_love_song_count
 * @method static Builder|LastFmUser newModelQuery()
 * @method static Builder|LastFmUser newQuery()
 * @method static Builder|LastFmUser query()
 * @method static Builder|LastFmUser whereAge($value)
 * @method static Builder|LastFmUser whereAlbumCount($value)
 * @method static Builder|LastFmUser whereArtistCount($value)
 * @method static Builder|LastFmUser whereBootstrap($value)
 * @method static Builder|LastFmUser whereCountry($value)
 * @method static Builder|LastFmUser whereCreatedAt($value)
 * @method static Builder|LastFmUser whereGender($value)
 * @method static Builder|LastFmUser whereId($value)
 * @method static Builder|LastFmUser whereImage($value)
 * @method static Builder|LastFmUser whereName($value)
 * @method static Builder|LastFmUser wherePlaycount($value)
 * @method static Builder|LastFmUser wherePlaylists($value)
 * @method static Builder|LastFmUser whereRealname($value)
 * @method static Builder|LastFmUser whereRegistered($value)
 * @method static Builder|LastFmUser whereSubscriber($value)
 * @method static Builder|LastFmUser whereTrackCount($value)
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
        'playcount',
        'artist_count',
        'playlists',
        'track_count',
        'album_count',
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
    public function lastFmLoveSong() : HasMany
    {
        return $this->hasMany(LastFmLoveSong::class);
    }
}
