<?php

namespace Tatter\Visits\Config;

use CodeIgniter\Config\BaseConfig;

class Visits extends BaseConfig
{
    /**
     * Metric for tracking a unique visitor
     *
     * @var 'ip_address'|'session_id'|'user_id'
     */
    public $trackingMethod = 'ip_address';

    /**
     * Session variable to check for a logged-in user ID
     *
     * @deprecated Next version will rely on codeigniter4/authentication-implementation
     *
     * @var string
     */
    public $userSource = 'logged_in';

    /**
     * Number of minutes before a visit counts as new
     * instead of incrementing a previous view count.
     * Set to zero to record each page view as unique (not recommended).
     *
     * @var int
     */
    public $resetMinutes = 60;

    /**
     * Whether to ignore AJAX requests when recording.
     * See framework User Guide for caveats.
     *
     * @see https://www.codeigniter.com/user_guide/general/ajax.html
     *
     * @var bool
     */
    public $ignoreAjax = true;
}
