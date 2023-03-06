<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\LastFmImageArtist
 *
 * @property int $id
 * @property int $last_fm_artist_id
 * @property string $image
 * @property string $size
 * @property int|null $actual
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\LastFmArtist|null $artist
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmImageArtist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmImageArtist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmImageArtist query()
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmImageArtist whereActual($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmImageArtist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmImageArtist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmImageArtist whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmImageArtist whereLastFmArtistId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmImageArtist whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmImageArtist whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LastFmImageArtist extends Model
{
    use HasFactory;

    protected $fillable = [
        'last_fm_artist_id',
        'image',
        'size',
    ];

    /**
     * @return BelongsTo
     */
    public function artist() : BelongsTo
    {
        return $this->belongsTo(LastFmArtist::class);
    }
}
