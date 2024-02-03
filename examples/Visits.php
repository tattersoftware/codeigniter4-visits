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
     * Database field for tracking a unique visitor
     *
     * @var 'ip_address'|'session_id'|'user_id'
     */
    public string $trackingMethod = 'ip_address';

    /**
     * Number of seconds before a visit counts as new
     * instead of incrementing a previous view count.
     * Set to zero to record each page view as unique (not recommended).
     */
    public int $resetAfter = HOUR;

    /**
     * Transformers to apply (in order) before
     * recording the visit data.
     *
     * @see VisitModel::applyTransformations()
     *
     * @var class-string<Transformer>[]
     */
    public array $transformers = [];

    /**
     * Whether to ignore AJAX requests when recording.
     * See framework User Guide for caveats.
     *
     * @see https://www.codeigniter.com/user_guide/general/ajax.html
     */
    public bool $ignoreAjax = true;

    /**
     * Whether to ignore requests that result in a redirect response.
     * Note: requires using the "after" filter method.
     */
    public bool $ignoreRedirects = true;

    /**
     * Whether to ignore requests that result in an empty body.
     * Note: requires using the "after" filter method.
     */
    public bool $requireBody = false;

    /**
     * Whether to ignore requests with Content Types other than HTML.
     * Note: requires using the "after" filter method.
     */
    public bool $requireHtml = false;
}
