<?php

namespace Tatter\Visits\Config;

use Tatter\Visits\Filters\VisitsFilter;

class Registrar
{
    /**
     * Registers the Shield filters.
     */
    public static function Filters(): array
    {
        return [
            'aliases' => ['visits' => VisitsFilter::class],
        ];
    }
}
