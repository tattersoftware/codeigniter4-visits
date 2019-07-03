<?php namespace Tatter\Visits\Config;

use CodeIgniter\Events\Events;
use CodeIgniter\Config\Services;

Events::on('post_controller_constructor', function () {
	Services::visits()->record();
});
