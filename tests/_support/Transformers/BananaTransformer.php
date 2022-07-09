<?php

namespace Tests\Support\Transformers;

use CodeIgniter\HTTP\IncomingRequest;
use Tatter\Visits\Entities\Visit;
use Tatter\Visits\Interfaces\Transformer;

abstract class BananaTransformer implements Transformer
{
    /**
     * Makes the visit fruity.
     */
    public static function transform(Visit $visit, IncomingRequest $request): Visit
    {
        $keys = [
            'user_agent',
            'scheme',
            'host',
            'port',
            'user',
            'pass',
            'path',
            'query',
            'fragment',
        ];

        foreach ($keys as $key) {
            $visit->{$key} = 'banana';
        }

        return $visit;
    }
}
