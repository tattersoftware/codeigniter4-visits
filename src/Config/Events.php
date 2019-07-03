<?php namespace Tatter\Visits\Config;

use CodeIgniter\Events\Events;
use CodeIgniter\Config\Services;

Events::on('post_controller_constructor', function () {
	// Ignore CLI requests
	return is_cli() ?: Services::visits()->record();
});
