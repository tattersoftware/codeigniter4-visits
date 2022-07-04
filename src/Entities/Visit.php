<?php

namespace Tatter\Visits\Entities;

use CodeIgniter\Entity\Entity;
use Tatter\Visits\Models\VisitModel;

class Visit extends Entity
{
    protected $dates = [
        'created_at',
        'verified_at',
    ];

    // magic IP string/long converters
    public function setIpAddress($ipAddress)
    {
        $this->attributes['ip_address'] = ($long = ip2long($ipAddress)) ? $long : $ipAddress;

        return $this;
    }

    public function getIpAddress(string $format = 'long')
    {
        if ($format === 'string') {
            return long2ip($this->attributes['ip_address']);
        }

        return $this->attributes['ip_address'];
    }

    // search for a visit with similar characteristics to the current one
    public function getSimilar($trackingMethod, $resetMinutes = 60)
    {
        // required fields
        if (empty($this->host) || empty($this->path)) {
            return false;
        }
        // require tracking field
        if (empty($this->{$trackingMethod})) {
            return false;
        }

        $visits = new VisitModel();
        // check for matching components within the last resetMinutes
        $since = date('Y-m-d H:i:s', strtotime('-' . $resetMinutes . ' minutes'));

        return $visits->where('host', $this->host)
            ->where('path', $this->path)
            ->where('query', (string) $this->query)
            ->where($trackingMethod, $this->{$trackingMethod})
            ->where('created_at >=', $since)
            ->first();
    }
}
