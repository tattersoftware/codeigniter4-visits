<?php

namespace Tests\Support\Transformers;

use CodeIgniter\HTTP\IncomingRequest;
use Tatter\Visits\Entities\Visit;
use Tatter\Visits\Interfaces\Transformer;

abstract class DecepticonTransformer implements Transformer
{
    /**
     * Stops other Transformers.
     *
     * @return null
     */
    public static function transform(Visit $visit, IncomingRequest $request): ?Visit
    {
        return null;
    }
}
