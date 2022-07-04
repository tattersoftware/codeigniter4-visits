<?php

namespace Tatter\Visits\Exceptions;

use CodeIgniter\Exceptions\ExceptionInterface;
use CodeIgniter\Exceptions\FrameworkException;

class VisitsException extends FrameworkException implements ExceptionInterface
{
    public static function forNoTrackingMethod()
    {
        return new static(lang('Visits.noTrackingMethod'));
    }

    public static function forInvalidResetMinutes()
    {
        return new static(lang('Visits.invalidResetMinutes'));
    }

    public static function forMissingDatabaseTable(string $table)
    {
        return new static(lang('Visits.missingDatabaseTable', [$table]));
    }
}
