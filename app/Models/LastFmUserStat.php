<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\LastFmUserStat
 *
 * @property int $id
 * @property int $last_fm_user_id
 * @property int $playcount
 * @property int $artist_count
 * @property int $playlists
 * @property int $track_count
 * @property int $album_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\LastFmUser|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUserStat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUserStat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUserStat query()
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUserStat whereAlbumCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUserStat whereArtistCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUserStat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUserStat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUserStat whereLastFmUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUserStat wherePlaycount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUserStat wherePlaylists($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUserStat whereTrackCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmUserStat whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LastFmUserStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'playcount',
        'artist_count',
        'playlists',
        'track_count',
        'album_count',
    ];

    /**
     * @return BelongsTo
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(LastFmUser::class, 'last_fm_user_id');
    }
}
