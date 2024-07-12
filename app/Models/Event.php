<?php

namespace App\Models;

use Base\Models\BaseEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property-read string $src
 * @property Venue $venue
 * @property File $poster
 */
class Event extends BaseEvent
{
    use HasFactory;

    /**
     * @inheritdoc
     */
    protected static function boot(): void
    {
        parent::boot();

        static::deleted(function (self $model) {
            $model->poster && $model->poster->delete();
        });
    }
}
