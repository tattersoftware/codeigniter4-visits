<?php

namespace Tatter\Visits\Config;

use CodeIgniter\Events\Events;
use Config\Services;

Events::on('post_controller_constructor', function () {
    $config = config('Visits');
    // Ignore CLI requests
    if (!is_cli() and $config->trackAllPages) {
        Services::visits()->record();
    }
});
