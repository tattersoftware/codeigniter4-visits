<?php namespace Tatter\Visits\Config;

use CodeIgniter\Config\BaseConfig;

class Visits extends BaseConfig
{
	// metric for tracking a unique visitor
	// one of: ip_address, session_id, user_id
	public $trackingMethod  = "ip_address";
	
	// the session variable to check for a logged-in user ID
	public $userSource = 'visitsUserId';
	
	// how many minutes before a visit counts as new instead of incrementing a previous view count
	// set to zero to record each page view as unique (not recommended)
	public $resetMinutes = 60;
}
