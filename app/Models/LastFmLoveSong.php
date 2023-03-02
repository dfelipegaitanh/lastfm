<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\LastFmLoveSong
 *
 * @method static Builder|LastFmLoveSong newModelQuery()
 * @method static Builder|LastFmLoveSong newQuery()
 * @method static Builder|LastFmLoveSong query()
 * @property int $id
 * @property int $last_fm_song_id
 * @property int $last_fm_user_id
 * @property mixed $date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|LastFmLoveSong whereCreatedAt($value)
 * @method static Builder|LastFmLoveSong whereDate($value)
 * @method static Builder|LastFmLoveSong whereId($value)
 * @method static Builder|LastFmLoveSong whereLastFmSongId($value)
 * @method static Builder|LastFmLoveSong whereLastFmUserId($value)
 * @method static Builder|LastFmLoveSong whereUpdatedAt($value)
 * @property-read LastFmUser|null $lastFmUser
 * @property-read LastFmSong|null $lastFmSong
 * @property string $uts
 * @property string $date_text
 * @method static Builder|LastFmLoveSong whereDateText($value)
 * @method static Builder|LastFmLoveSong whereUts($value)
 * @mixin Eloquent
 */
class LastFmLoveSong extends Model
{
    use HasFactory;

    protected $fillable = [
        'last_fm_song_id',
        'last_fm_user_id',
    ];

    /**
     * @return BelongsTo
     */
    public function lastFmUser() : BelongsTo
    {
        return $this->belongsTo(LastFmUser::class);
    }

    /**
     * @return BelongsTo
     */
    public function lastFmSong() : BelongsTo
    {
        return $this->belongsTo(LastFmSong::class);
    }

}
