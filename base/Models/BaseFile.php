<?php

namespace Base\Models;

use Base\Custom\Illuminate\Database\Eloquent\Model;
use Base\Models\BaseEvent as Event;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property string $base
 * @property string $path
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Event $event
 */
abstract class BaseFile extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['base', 'path', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return HasOne
     */
    public function event(): HasOne
    {
        return $this->hasOne(Event::class, 'poster_id');
    }
}
