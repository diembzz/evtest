<?php

namespace Base\Custom\Illuminate\Database\Eloquent;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

abstract class Model extends \Illuminate\Database\Eloquent\Model
{
    /**
     * @inheritdoc
     */
    public function belongsTo(
        $related,
        $foreignKey = null,
        $ownerKey = null,
        $relation = null,
    ): BelongsTo {
        return parent::belongsTo(
            $this->currentNamespaceClass($related),
            $foreignKey,
            $ownerKey,
            $relation,
        );
    }

    /**
     * @inheritdoc
     */
    public function belongsToMany(
        $related,
        $table = null,
        $foreignPivotKey = null,
        $relatedPivotKey = null,
        $parentKey = null,
        $relatedKey = null,
        $relation = null,
    ): BelongsToMany {
        return parent::belongsToMany(
            $this->currentNamespaceClass($related),
            $table,
            $foreignPivotKey,
            $relatedKey,
            $relation,
        );
    }

    /**
     * @inheritdoc
     */
    public function hasMany(
        $related,
        $foreignKey = null,
        $localKey = null,
    ): HasMany {
        return parent::hasMany(
            $this->currentNamespaceClass($related),
            $foreignKey,
            $localKey,
        );
    }

    /**
     * @inheritdoc
     */
    public function hasManyThrough(
        $related,
        $through,
        $firstKey = null,
        $secondKey = null,
        $localKey = null,
        $secondLocalKey = null,
    ): HasManyThrough {
        return parent::hasManyThrough(
            $this->currentNamespaceClass($related),
            $through,
            $firstKey,
            $secondKey,
            $localKey,
            $secondLocalKey,
        );
    }

    /**
     * @inheritdoc
     */
    public function hasOne(
        $related,
        $foreignKey = null,
        $localKey = null,
    ): HasOne {
        return parent::hasOne(
            $this->currentNamespaceClass($related),
            $foreignKey,
            $localKey,
        );
    }

    /**
     * @inheritdoc
     */
    public function hasOneThrough(
        $related,
        $through,
        $firstKey = null,
        $secondKey = null,
        $localKey = null,
        $secondLocalKey = null,
    ): HasOneThrough {
        return parent::hasOneThrough(
            $this->currentNamespaceClass($related),
            $through,
            $firstKey,
            $secondKey,
            $localKey,
            $secondLocalKey,
        );
    }

    /**
     * @inheritdoc
     */
    protected function guessBelongsToRelation(): string
    {
        [$one, $two, $three, $caller] = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4);

        return $caller['function'];
    }

    /**
     * Modify model class namespace by current model class instance
     *
     * @param string $class
     * @return string
     */
    private function currentNamespaceClass(string $class): string
    {
        $name = substr(class_basename($class), strlen('Base'));
        $ns = substr(static::class, 0, -strlen(class_basename(static::class)));

        return $ns . $name;
    }
}
