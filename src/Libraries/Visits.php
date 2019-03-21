<?php namespace Tatter\Libraries;

/***
* Name: Visits
* Author: Matthew Gatner
* Contact: mgatner@tattersoftware.com
* Created: 2019-02-12
*
* Description:  Lightweight traffic tracking library for CodeIgniter 4
*
* Requirements:
* 	>= PHP 7.1
* 	>= CodeIgniter 4.0
*	Preconfigured, autoloaded Database
*	CodeIgniter's URL helper (loaded automatically)
*	Function userId() to return ID of logged in user
*	Visits table (see example below)
*
* Configuration:
* 	Use Config/Visits.php to override default behavior
*
* Tables:

CREATE TABLE IF NOT EXISTS `visits` (
	`id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`date` DATE NOT NULL,
	`class` VARCHAR(63) NOT NULL,
	`method` VARCHAR(63) NOT NULL,
	`item` VARCHAR(63) NULL,
	`user_id` INT NOT NULL,
	`ip_address` BIGINT NULL,
	`tally` INT NOT NULL,
	`created_at` DATETIME NOT NULL,
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	INDEX(`date`), INDEX(`user_id`), INDEX(`class`,`method`,`item`), INDEX(`ip_address`),
	INDEX(`created_at`), INDEX(`updated_at`)
);

*
* @package CodeIgniter4-Assets
* @author Matthew Gatner
* @link https://github.com/tattersoftware/codeigniter4-visits
*
***/

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Config\Services;
use Tatter\Entities\Visit;
use Tatter\Models\VisitModel;
use Tatter\Visits\Exceptions\VisitsException;

/*** CLASS ***/
class Visits
{
	/**
	 * Our configuration instance.
	 *
	 * @var \Config\Visits
	 */
	protected $config;

	// initiate library, check for existing session
	public function __construct(BaseConfig $config)
	{
		// ignore CLI requests
		if (is_cli())
			return;
		
		// save configuration
		$this->config = $config;
		
		// validate config
		if (empty($this->config->trackingMethod))
		{
			throw VisitsException::forNoTrackingMethod();
		}
		if (! is_numeric($this->config->resetMinutes))
		{
			throw VisitsException::forInvalidResetMinutes();
		}

		// initiate the Session library
		$this->session = Services::session();
	}
	
	// record add a new visit, or increase the view count on an existing one
	public function record()
	{
		$visits = new VisitModel();
		$visit = new Visit();
		
		// start the object with parsed URL components (https://secure.php.net/manual/en/function.parse-url.php)
		$visit->fill(parse_url( current_url() ));
		
		// add session/server specifics
		$visit->session_id = $this->session->session_id;
		$visit->user_id = $this->session->userId ?? null;
		$visit->user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
		$visit->ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
		
		// check for an existing similar record
		if ($similar = $this->similar($visit)):
			// increment number of views and update
			$similar->views++;
			$visits->save($similar);
			return $similar->id;
		endif;
		
		// create a new visit record
		$visits->save($visit);
		return $visits->getInsertId();
	}
	
	// search for a visit with similar characteristics to an existing one
	protected function similar(Visit $visit)
	{
		// required fields
		if (empty($visit->host) || empty($visit->path))
			return false;
		// require tracking field
		if (empty($visit->{$this->config->trackingMethod}))
			return false;

		$visits = new VisitModel();		

		// check for matching components within the last resetMinutes
		$since = date("Y-m-d H:i:s", strtotime("-" . $this->config->resetMinutes . " minutes"));
		$similar = $visits->where('host', $visit->host)
		                  ->where('path', $visit->path)
		                  ->where('query', (string)$visit->query)
		                  ->where($this->config->trackingMethod, $visit->{$this->config->trackingMethod})
		                  ->where("created_at >=", $since)
		                  ->first();

		return $similar;
	}	
	
	protected function migrate()
	{
		return "disabled";
		
		$migrate = Services::migrations();

		try
		{
			$migrate->current();
		}
			catch (\Exception $e)
		{
			// Do something with the error here...
		}
	}
}
