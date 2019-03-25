<?php namespace Tatter\Config;

use CodeIgniter\Config\BaseService;

class Services extends BaseService
{
    public static function visits(BaseConfig $config = null, bool $getShared = true)
    {
		if ($getShared):
			return static::getSharedInstance('visits', $config);
		endif;

		// prioritizes user config in app/Config if found
		if (empty($config)):
			if (class_exists('\Config\Visits')):
				$config = new \Config\Visits();
			else:
				$config = new \Tatter\Config\Visits();
			endif;
		endif;

		return new \Tatter\Visits($config);
	}
}
