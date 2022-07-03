<?php

namespace Tatter\Visits\Models;

use CodeIgniter\Model;

class VisitModel extends Model
{
    protected $table          = 'visits';
    protected $primaryKey     = 'id';
    protected $returnType     = 'Tatter\Visits\Entities\Visit';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = false;
    protected $skipValidation = false;
    protected $allowedFields  = [
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
}
