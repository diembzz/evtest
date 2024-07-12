<?php

namespace Base\Models;

use Base\Custom\Illuminate\Database\Eloquent\Model;
use Base\Models\BaseEvent as Event;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $base
 * @property string $path
 * @property string $created_at
 * @property string $updated_at
 * @property Event[] $events
 */
abstract class BaseFile extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['base', 'path', 'created_at', 'updated_at'];

    /**
     * @return HasMany
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'poster_id');
    }
}
