<?php namespace Tatter\Visits\Config;

use Config\Services as BaseServices;
use Tatter\Visits\Config\Visits as VisitsConfig;
use Tatter\Visits\Visits;

class Services extends BaseServices
{
	public static function visits(VisitsConfig $config = null, bool $getShared = true)
	{
		if ($getShared)
		{
			return static::getSharedInstance('visits', $config);
		}

		$config ??= config('Visits');

		return new Visits($config);
	}
}
