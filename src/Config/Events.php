<?php

namespace Tatter\Visits\Config;

use CodeIgniter\Events\Events;

Events::on('post_controller_constructor', static function () {
    $config = config('Visits');
    // Ignore CLI requests
    if (! is_cli() && $config->trackAllPages) {
        Services::visits()->record();
    }
});
