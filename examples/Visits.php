<?php

namespace Config;

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

use Tatter\Visits\Config\Visits as BaseConfig;

class Visits extends BaseConfig
{
    /**
     * Whether to enable tracking in all controllers using
     * the post_controller_constructor event.
     */
    public bool $trackAllPages = true;

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

    /**
     * URIs to exclude from tracking.
     * Accepts regex values.
     *
     * @var string[]
     */
    public array $excludeUris = [];
}
