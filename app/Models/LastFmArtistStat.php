<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\LastFmArtistStat
 *
 * @property int $id
 * @property int $last_fm_artist_id
 * @property int $last_fm_user_id
 * @property int $userplaycount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\LastFmUser|null $user
 * @method static Builder|LastFmArtistStat newModelQuery()
 * @method static Builder|LastFmArtistStat newQuery()
 * @method static Builder|LastFmArtistStat query()
 * @method static Builder|LastFmArtistStat whereCreatedAt($value)
 * @method static Builder|LastFmArtistStat whereId($value)
 * @method static Builder|LastFmArtistStat whereLastFmArtistId($value)
 * @method static Builder|LastFmArtistStat whereLastFmUserId($value)
 * @method static Builder|LastFmArtistStat whereUpdatedAt($value)
 * @method static Builder|LastFmArtistStat whereUserplaycount($value)
 * @mixin Eloquent
 */
class LastFmArtistStat extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(LastFmUser::class);
    }

}
