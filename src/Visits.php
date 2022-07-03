<?php

namespace Tatter\Visits;

use CodeIgniter\Config\Services;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Session\Session;
use Tatter\Visits\Config\Visits as VisitsConfig;
use Tatter\Visits\Entities\Visit;
use Tatter\Visits\Exceptions\VisitsException;
use Tatter\Visits\Models\VisitModel;

class Visits
{
    /**
     * Our configuration instance.
     *
     * @var VisitsConfig
     */
    protected $config;

    /**
     * The main database connection, needed to store records.
     *
     * @var ConnectionInterface
     */
    protected $db;

    /**
     * The active user session, for session data and tracking.
     *
     * @var Session
     */
    protected $session;

    // initiate library, check for existing session
    public function __construct(VisitsConfig $config, $db = null)
    {
        // ignore CLI requests
        if (is_cli()) {
            return;
        }

        // save configuration
        $this->config = $config;

        // initiate the Session library
        $this->session = Services::session();

        // If no db connection passed in, use the default database group.
        $this->db = db_connect($db);

        /**
         * @phpstan-ignore-next-line
         * @psalm-suppress InaccessibleProperty
         */
        $table = model(VisitModel::class)->table;
        if (! $this->db->tableExists($table)) {
            throw VisitsException::forMissingDatabaseTable($table);
        }

        // @phpstan-ignore-next-line
        if (empty($this->config->trackingMethod)) {
            throw VisitsException::forNoTrackingMethod();
        }

        if (! is_numeric($this->config->resetMinutes)) {
            throw VisitsException::forInvalidResetMinutes();
        }
    }

    // add a new visit, or increase the view count on an existing one
    public function record()
    {
        // Ignore CLI requests
        if (is_cli()) {
            return;
        }

        // Check for ignored AJAX requests
        if (Services::request()->isAJAX() && $this->config->ignoreAjax) {
            return;
        }

        $visits = new VisitModel();
        $visit  = new Visit();

        // start the object with parsed URL components (https://secure.php.net/manual/en/function.parse-url.php)
        $visit->fill(parse_url(current_url()));

        // Check if URI has been whitelisted from Visit check
        if ($exclude_uris = $this->config->excludeUris) {
            foreach ($exclude_uris as $excluded) {
                if (url_is($excluded)) {
                    return $this;
                }
            }
        }
        // add session/server specifics
        $visit->session_id = $this->session->session_id;
        $visit->user_id    = $this->session->{$this->config->userSource} ?? null; // @phpstan-ignore-line
        $visit->user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $visit->ip_address = $_SERVER['REMOTE_ADDR'] ?? null;

        // check for an existing similar record
        if ($similar = $visit->getSimilar($this->config->trackingMethod, $this->config->resetMinutes)) {
            // increment number of views and update
            $similar->views++;
            $visits->save($similar);

            return $similar;
        }

        // create a new visit record
        $visits->save($visit);

        return $visit;
    }
}
