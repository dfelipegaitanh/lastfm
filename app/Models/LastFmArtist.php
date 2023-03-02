<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\LastFmArtist
 *
 * @property int $id
 * @property string $mbid
 * @property string $name
 * @property string $url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|LastFmArtist newModelQuery()
 * @method static Builder|LastFmArtist newQuery()
 * @method static Builder|LastFmArtist query()
 * @method static Builder|LastFmArtist whereCreatedAt($value)
 * @method static Builder|LastFmArtist whereId($value)
 * @method static Builder|LastFmArtist whereMbid($value)
 * @method static Builder|LastFmArtist whereName($value)
 * @method static Builder|LastFmArtist whereUpdatedAt($value)
 * @method static Builder|LastFmArtist whereUrl($value)
 * @property-read Collection<int, LastFmSong> $lastFmSongs
 * @property-read int|null $last_fm_songs_count
 * @property-read Collection<int, LastFmSong> $lastFmSongs
 * @property-read Collection<int, LastFmSong> $lastFmSongs
 * @property-read Collection<int, LastFmSong> $lastFmSongs
 * @property-read Collection<int, LastFmSong> $lastFmSongs
 * @property-read Collection<int, LastFmSong> $lastFmSongs
 * @property-read Collection<int, \App\Models\LastFmSong> $lastFmSongs
 * @mixin Eloquent
 */
class LastFmArtist extends Model
{
    use HasFactory;

    protected $fillable = [
        'mbid',
        'name',
        'url',
    ];

    public function lastFmSongs()
    {
        return $this->hasMany(LastFmSong::class);
    }

}
