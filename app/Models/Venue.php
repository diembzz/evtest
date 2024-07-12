<?php

namespace App\Models;

use Base\Models\BaseVenue;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property Event[] $events
 */
class Venue extends BaseVenue
{
    use HasFactory;

    /**
     * @inheritdoc
     */
    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function (self $model) {
            foreach ($model->events as $model) {
                $model->delete();
            }
        });
    }
}
