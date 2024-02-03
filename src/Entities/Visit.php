<?php

namespace Tatter\Visits\Entities;

use CodeIgniter\Entity\Entity;

/**
 * @property int $views
 */
class Visit extends Entity
{
    protected $dates = [
        'created_at',
        'verified_at',
    ];
    protected $casts = [
        'session_id' => 'string',
        'user_id'    => '?int',
        'user_agent' => 'string',
        'scheme'     => 'string',
        'host'       => 'string',
        'port'       => 'string',
        'user'       => 'string',
        'pass'       => 'string',
        'path'       => 'string',
        'query'      => 'string',
        'fragment'   => 'string',
        'views'      => 'int',
    ];

    /**
     * Converts string IP addresses to their database integer format.
     *
     * @param int|string|null $ipAddress
     */
    public function setIpAddress($ipAddress): void
    {
        if (is_string($ipAddress)) {
            $this->attributes['ip_address'] = ip2long($ipAddress) ?: null;

            return;
        }

        if (is_int($ipAddress) && long2ip($ipAddress)) {
            $this->attributes['ip_address'] = $ipAddress;

            return;
        }

        $this->attributes['ip_address'] = null;
    }

    /**
     * Converts integer IP addresses to their human pointed format.
     */
    public function getIpAddress(): ?string
    {
        if (is_numeric($this->attributes['ip_address'])) {
            return long2ip($this->attributes['ip_address']);
        }

        return null;
    }
}
