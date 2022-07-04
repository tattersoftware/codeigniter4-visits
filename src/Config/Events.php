<?php

namespace Tatter\Visits\Config;

use CodeIgniter\Events\Events;

Events::on('post_controller_constructor', static fn () => // Ignore CLI requests
is_cli() ?: service('visits')->record());
