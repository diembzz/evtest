<?php

namespace Base\Custom\Spatie\QueryBuilder\Sorts;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class RelationSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property): Builder
    {
        return $query->orderByPowerJoins($property, $descending ? "desc" : "asc");
    }
}
