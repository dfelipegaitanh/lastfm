<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
