<?php namespace Tatter\Visits\Entities;

use CodeIgniter\Entity;
use Tatter\Visits\Models\VisitModel;

class Visit extends Entity
{
	protected $id;
	protected $session_id;
	protected $user_id;
	protected $ip_address;
	protected $user_agent;
	protected $scheme = '';
	protected $host;
	protected $port = '';
	protected $user = '';
	protected $pass = '';
	protected $path;
	protected $query = '';
	protected $fragment = '';
	protected $views = 0;
	protected $created_at;
	protected $updated_at;
	
	protected $_options = [
		'dates' => ['created_at', 'verified_at'],
		'casts' => [ ],
		'datamap' => [ ]
	];
	
	// magic IP string/long converters
	public function setIpAddress($ipAddress)
	{
		if ($long = ip2long($ipAddress))
			$this->ip_address = $long;
		else
			$this->ip_address = $ipAddress;
		return $this;
	}

	public function getIpAddress(string $format = 'long')
	{
		if ($format=="string")
			return long2ip($this->ip_address);
		else
			return $this->ip_address;
	}
	
	// magic timezone helpers
	public function setCreatedAt(string $dateString)
	{
		$this->created_at = new Time($dateString, 'UTC');

		return $this;
	}

	public function getCreatedAt(string $format = 'Y-m-d H:i:s')
	{
		// Convert to CodeIgniter\I18n\Time object
		$this->created_at = $this->mutateDate($this->created_at);

		$timezone = $this->timezone ?? app_timezone();

		$this->created_at->setTimezone($timezone);

		return $this->created_at->format($format);
	}
	
	// search for a visit with similar characteristics to the current one
	public function getSimilar($trackingMethod, $resetMinutes = 60)
	{
		// required fields
		if (empty($this->host) || empty($this->path))
			return false;
		// require tracking field
		if (empty($this->{$trackingMethod}))
			return false;

		$visits = new VisitModel();		

		// check for matching components within the last resetMinutes
		$since = date("Y-m-d H:i:s", strtotime("-" . $resetMinutes . " minutes"));
		$similar = $visits->where('host', $this->host)
		                  ->where('path', $this->path)
		                  ->where('query', (string)$this->query)
		                  ->where($trackingMethod, $this->{$trackingMethod})
		                  ->where("created_at >=", $since)
		                  ->first();

		return $similar;
	}
}
