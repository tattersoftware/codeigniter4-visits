<?php namespace Tatter\Config;

use CodeIgniter\Config\BaseConfig;

class Visits extends BaseConfig
{
	// metric for tracking a unique visitor
	// one of: ip_address, session_id, user_id
	public $trackingMethod  = "session_id";
	
	// how many minutes before a visit counts as new instead of incrementing a previous view count
	// set to zero to record each page view as unique (not recommended)
	public $resetMinutes = 60;
}
