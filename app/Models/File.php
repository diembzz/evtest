<?php

namespace App\Models;

use Base\Models\BaseFile;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property Event[] $events
 */
class File extends BaseFile
{
    use SoftDeletes;

    public const PUBLIC_PATH = 'storage/app/public';

    /**
     * @inheritdoc
     */
    protected $appends = [
        'src'
    ];

    /**
     * @return Attribute
     */
    protected function src(): Attribute
    {
        return Attribute::make(
            get: fn() => config('app.url') . '/' . $this->base . '/' . $this->path,
        );
    }
}
