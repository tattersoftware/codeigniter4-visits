<?php

namespace Tatter\Visits\Models;

use CodeIgniter\Model;
use Tatter\Visits\Entities\Visit;

class VisitModel extends Model
{
    protected $table         = 'visits';
    protected $primaryKey    = 'id';
    protected $returnType    = Visit::class;
    protected $useTimestamps = true;
    protected $beforeInsert  = ['applyTransformations'];
    protected $allowedFields = [
        'session_id',
        'user_id',
        'ip_address',
        'user_agent',
        'views',
        'scheme',
        'host',
        'port',
        'user',
        'pass',
        'path',
        'query',
        'fragment',
    ];
    protected $validationRules = [
        'host' => 'required',
        'path' => 'required',
    ];

    /**
     * Runs transformations as defined in the Config file
     * sequentially on Visit data, returning the modified results.
     */
    final protected function applyTransformations(array $eventData): array
    {
        foreach (config('Visits')->transformers as $transformer) {
            $eventData['data'] = $transformer::transform($eventData['data']);
        }

        return $eventData;
    }
}
