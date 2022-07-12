<?php

namespace Tatter\Visits\Models;

use CodeIgniter\HTTP\IncomingRequest;
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
        'host'       => 'required',
        'path'       => 'required',
        'session_id' => 'permit_empty|max_length[127]',
    ];

    /**
     * Parses the current URL and adds relevant
     * Request info to create an Visit.
     */
    public function makeFromRequest(IncomingRequest $request): Visit
    {
        // Get the URI of the current Request
        $uri = current_url(true, $request);

        /**
         * Only try to identify a current user if the appropriate helper is defined
         *
         * @see https://codeigniter4.github.io/CodeIgniter4/extending/authentication.html
         */
        $userId = function_exists('user_id') ? user_id() : null;

        return new Visit([
            'scheme'     => $uri->getScheme(),
            'host'       => $uri->getHost(),
            'port'       => $uri->getPort() ?? '',
            'user'       => $uri->showPassword(false)->getUserInfo() ?? '',
            'path'       => $uri->getPath(),
            'query'      => $uri->getQuery(),
            'fragment'   => $uri->getFragment(),
            'session_id' => session_id(),
            'user_id'    => $userId,
            'user_agent' => $request->getServer('HTTP_USER_AGENT') ?? '',
            'ip_address' => $request->getServer('REMOTE_ADDR'),
        ]);
    }

    /**
     * Finds the first visit with similar characteristics
     * based on the configuration settings.
     */
    public function findSimilar(Visit $visit): ?Visit
    {
        $config   = config('Visits');
        $tracking = $visit->toRawArray()[$config->trackingMethod] ?? null;

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
            'user_id'    => random_int(1, 100),
            'ip_address' => ip2long($faker->ipv4),
            'user_agent' => $faker->userAgent,
            'views'      => random_int(0, 4),
            'scheme'     => random_int(0, 3) ? 'https' : 'http',
            'host'       => $faker->domainName,
            'port'       => '',
            'user'       => '',
            'pass'       => '',
            'path'       => implode('/', $faker->words),
            'query'      => random_int(0, 5) ? '' : 'q=' . $faker->word,
            'fragment'   => random_int(0, 5) ? '' : '#' . $faker->word,
        ]);
    }
}
