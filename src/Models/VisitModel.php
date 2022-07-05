<?php

namespace Tatter\Visits\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;
use Faker\Generator;
use Tatter\Visits\Entities\Visit;

class VisitModel extends Model
{
    protected $table         = 'visits';
    protected $primaryKey    = 'id';
    protected $returnType    = Visit::class;
    protected $useTimestamps = true;
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
     * Finds the first visit with similar characteristics
     * based on the configuration settings.
     */
    public function findSimilar(Visit $visit): ?Visit
    {
        $config   = config('Visits');
        $tracking = $visit->{$config->trackingMethod};

        // Required fields
        if (empty($tracking) || empty($visit->host) || empty($visit->path)) {
            return null;
        }

        // Check for matching components within the configured period
        $since = Time::now()->subSeconds($config->resetAfter)->format('Y-m-d H:i:s');

        return $this->where('host', $visit->host)
            ->where('path', $visit->path)
            ->where('query', (string) $visit->query)
            ->where($config->trackingMethod, $tracking)
            ->where('created_at >=', $since)
            ->first();
    }

    /**
     * Faked data for Fabricator.
     */
    public function fake(Generator &$faker): Visit
    {
        return new Visit([
            'session_id' => $faker->md5,
            'user_id'    => mt_rand(1, 100),
            'ip_address' => ip2long($faker->ipv4),
            'user_agent' => $faker->userAgent,
            'views'      => mt_rand(0, 4),
            'scheme'     => mt_rand(0, 3) ? 'https' : 'http',
            'host'       => $faker->domainName,
            'port'       => '',
            'user'       => '',
            'pass'       => '',
            'path'       => implode('/', $faker->words),
            'query'      => mt_rand(0, 5) ? '' : 'q=' . $faker->word,
            'fragment'   => mt_rand(0, 5) ? '' : '#' . $faker->word,
        ]);
    }
}
