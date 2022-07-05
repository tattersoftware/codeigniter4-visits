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
}
