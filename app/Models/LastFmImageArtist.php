<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LastFmImageArtist extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo
     */
    public function artist() : BelongsTo
    {
        return $this->belongsTo(LastFmArtist::class);
    }
}
