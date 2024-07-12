<?php

namespace Base\Custom\Illuminate\Database\Schema;

use Illuminate\Database\Schema\ForeignIdColumnDefinition;

class Blueprint extends \Illuminate\Database\Schema\Blueprint
{
    /**
     * @param string $column
     * @param string|null $on
     * @param string $references
     *
     * @return ForeignIdColumnDefinition
     */
    public function constrained(string $column, string $on = null, string $references = 'id'): ForeignIdColumnDefinition
    {
        $field = $this->foreignId($column);

        $field->constrained($on, $references)
            ->restrictOnUpdate()
            ->restrictOnDelete();

        return $field;
    }
}
