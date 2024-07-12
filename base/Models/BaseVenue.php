<?php

namespace Base\Models;

use Base\Custom\Illuminate\Database\Eloquent\Model;
use Base\Models\BaseEvent as Event;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property Event[] $events
 */
abstract class BaseVenue extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'created_at', 'updated_at'];

    /**
     * @return HasMany
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
