<?php

namespace Tatter\Visits\Interfaces;

use CodeIgniter\HTTP\IncomingRequest;
use Tatter\Visits\Entities\Visit;

interface Transformer
{
    /**
     * Returns the updated Visit, or `null` to cancel recording.
     */
    public static function transform(Visit $visit, IncomingRequest $request): ?Visit;
}
