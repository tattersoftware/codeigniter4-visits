<?php

namespace Tatter\Visits\Config;

use CodeIgniter\Config\BaseConfig;

class Visits extends BaseConfig
{
    // Enable tracking in all controllers using the post_controller_constructor event
    public $trackAllPages = true;

    // metric for tracking a unique visitor
    // one of: ip_address, session_id, user_id
    public $trackingMethod = 'ip_address';

    // the session variable to check for a logged-in user ID
    public $userSource = 'logged_in';

    // how many minutes before a visit counts as new instead of incrementing a previous view count
    // set to zero to record each page view as unique (not recommended)
    public $resetMinutes = 60;

    // Whether to ignore AJAX requests when recording
    public $ignoreAjax = true;

    // Exclude uris from tracking. Accept regex values.
    public $excludeUris = array();
}
