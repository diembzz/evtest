<?php

namespace App\Models;

use Base\Models\BaseFile;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @property Event[] $events
 */
class File extends BaseFile
{
    public const PUBLIC_PATH = 'storage/app/public';

    /**
     * @inheritdoc
     */
    protected $appends = [
        'src'
    ];

    /**
     * @inheritdoc
     */
    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function ($model) {
            $file = base_path(self::PUBLIC_PATH . '/' . $model->base . '/' . $model->path);
            file_exists($file) && unlink($file);
        });
    }

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
