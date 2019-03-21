<?php namespace Tatter\Visits\Exceptions;

use CodeIgniter\Exceptions\ExceptionInterface;
use CodeIgniter\Exceptions\FrameworkException;

class VisitsException extends FrameworkException implements ExceptionInterface
{
	public static function forNoTrackingMethod()
	{
		return new static("No tracking method selected.");
	}

	public static function forInvalidResetMinutes(string $group = null)
	{
		return new static("Minutes-to-reset must be a positive integer or zero.");
	}
}
