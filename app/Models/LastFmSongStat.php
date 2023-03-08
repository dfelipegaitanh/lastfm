<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\LastFmSongStat
 *
 * @property int $id
 * @property int $last_fm_song_id
 * @property int $last_fm_user_id
 * @property int $last_fm_period_time_id
 * @property int $userplaycount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\LastFmUser $periodTime
 * @property-read \App\Models\LastFmSong|null $song
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSongStat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSongStat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSongStat query()
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSongStat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSongStat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSongStat whereLastFmPeriodTimeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSongStat whereLastFmSongId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSongStat whereLastFmUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSongStat whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmSongStat whereUserplaycount($value)
 * @mixin \Eloquent
 */
class LastFmSongStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'userplaycount',
        'last_fm_song_id',
        'last_fm_user_id',
        'last_fm_period_time_id',
    ];

    /**
     * @return BelongsTo
     */
    public function periodTime() : BelongsTo
    {
        return $this->belongsTo(LastFmUser::class, 'last_fm_period_time_id');
    }

    public function song() : BelongsTo
    {
        return $this->belongsTo(LastFmSong::class, 'last_fm_song_id');
    }
}
