<?php namespace Tatter\Config;

use CodeIgniter\Config\BaseService;

class Services extends BaseService
{
    public static function visits(BaseConfig $config = null, bool $getShared = true)
    {
		if ($getShared)
		{
			return static::getSharedInstance('visits', $config);
		}

		if (empty($config))
		{
			$config = new \Config\Visits();
		}

		return new \Tatter\Libraries\Visits($config);
	}
}