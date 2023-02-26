<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LastFmUser
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUser query()
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUser whereAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUser whereAlbumCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUser whereArtistCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUser whereBootstrap($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUser whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUser whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUser whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUser whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUser wherePlaycount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUser wherePlaylists($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUser whereRealname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUser whereRegistered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUser whereSubscriber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUser whereTrackCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUser whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUser whereUrl($value)
 * @mixin \Eloquent
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
}
