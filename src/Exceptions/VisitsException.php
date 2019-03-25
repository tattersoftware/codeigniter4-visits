<?php namespace Tatter\Exceptions;

use CodeIgniter\Exceptions\ExceptionInterface;
use CodeIgniter\Exceptions\FrameworkException;

class VisitsException extends FrameworkException implements ExceptionInterface
{
	public static function forNoTrackingMethod()
	{
		return new static("No tracking method selected.");
	}

	public static function forInvalidResetMinutes()
	{
		return new static("Minutes-to-reset must be a positive integer or zero.");
	}

	public static function forMissingDatabaseTable(string $table)
	{
		return new static("Table `{$table}` missing for visit storage");
	}
}
