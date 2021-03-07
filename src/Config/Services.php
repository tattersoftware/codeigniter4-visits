<?php namespace Tatter\Visits\Config;

use CodeIgniter\Config\BaseService;

class Services extends BaseService
{
	public static function visits(BaseConfig $config = null, bool $getShared = true)
	{
		if ($getShared):
			return static::getSharedInstance('visits', $config);
		endif;

		// If no config was injected then load one
		// Prioritizes app/Config if found
		if (empty($config))
		{
			$config = config('Visits');
		}

		return new \Tatter\Visits\Visits($config);
	}
}
