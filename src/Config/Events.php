<?php namespace Tatter\Visits\Config;

use CodeIgniter\Events\Events;
use Config\Services;

Events::on('post_controller_constructor', fn() => // Ignore CLI requests
is_cli() ?: service('visits')->record());
