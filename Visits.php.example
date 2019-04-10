<?php namespace Config;

/***
*
* This file contains example values to alter default library behavior.
* Recommended usage:
*	1. Copy the file to app/Config/Visits.php
*	2. Change any values
*	3. Remove any lines to fallback to defaults
*
* You can also change the name of the database table used to store
* visit records by extending VisitModel and supplying your own value to $table
*
***/

use CodeIgniter\Config\BaseConfig;

class Visits extends \Tatter\Visits\Config\Visits
{
	// metric for tracking a unique visitor, one of: ip_address, session_id, user_id
	// NOTE: using user_id will count empty $userSource values as unique visits
	public $trackingMethod  = "ip_address";
	
	// the session variable to check for a logged-in user ID
	public $userSource = 'visitsUserId';
	
	// how many minutes before a visit counts as new instead of incrementing a previous view count
	// set to zero to record each visit as its own page view (not recommended)
	public $resetMinutes = 60;
}
