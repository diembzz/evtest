<?php

namespace Base\Models;

use Base\Custom\Illuminate\Database\Eloquent\Model;
use Base\Models\BaseFile as File;
use Base\Models\BaseVenue as Venue;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $venue_id
 * @property int $poster_id
 * @property string $name
 * @property string $event_date
 * @property string $created_at
 * @property string $updated_at
 * @property Venue $venue
 * @property File $poster
 */
abstract class BaseEvent extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['venue_id', 'poster_id', 'name', 'event_date', 'created_at', 'updated_at'];

    /**
     * @return BelongsTo
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * @return BelongsTo
     */
    public function poster(): BelongsTo
    {
        return $this->belongsTo(File::class, 'poster_id');
    }
}
