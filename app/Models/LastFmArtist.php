<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LastFmArtist
 *
 * @property int $id
 * @property string $mbid
 * @property string $name
 * @property string $url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmArtist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmArtist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmArtist query()
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmArtist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmArtist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmArtist whereMbid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmArtist whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmArtist whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmArtist whereUrl($value)
 * @mixin \Eloquent
 */
class LastFmArtist extends Model
{
    use HasFactory;

    protected $fillable = [
        'mbid',
        'name',
        'url',
    ];
}
