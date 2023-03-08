<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LastFmPeriodTime
 *
 * @property int $id
 * @property string|null $dateStart
 * @property string|null $dateEnd
 * @property string $type
 * @property bool $is_completed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmPeriodTime newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmPeriodTime newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmPeriodTime query()
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmPeriodTime whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmPeriodTime whereDateEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmPeriodTime whereDateStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmPeriodTime whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmPeriodTime whereIsCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmPeriodTime whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LastFmPeriodTime whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LastFmPeriodTime extends Model
{
    use HasFactory;

    protected $casts = [
        'is_completed' => 'boolean'
    ];

    protected $fillable = [
        'dateStart',
        'dateEnd',
    ];
}
