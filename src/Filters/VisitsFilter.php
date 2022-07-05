<?php

namespace Tatter\Visits\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Session\Session;
use Tatter\Visits\Config\Visits as VisitsConfig;
use Tatter\Visits\Entities\Visit;
use Tatter\Visits\Exceptions\VisitsException;
use Tatter\Visits\Models\VisitModel;

/**
 * Visits Filter
 *
 * Records visits for matching routes.
 */
class VisitsFilter implements FilterInterface
{
    /**
     * @codeCoverageIgnore
     *
     * @param mixed|null $arguments
     */
    public function before(RequestInterface $request, $arguments = null): void
    {
    }

    /**
     * Gathers the route-specific assets and adds their tags to the response.
     *
     * @param class-string<Bundle>[]|null $arguments Additional Bundle classes
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
    {
        // Ignore irrelevent responses
        if ($response instanceof RedirectResponse || empty($response->getBody())) {
            return;
        }

        // Check CLI separately for coverage
        if (is_cli() && ENVIRONMENT !== 'testing') {
            return; // @codeCoverageIgnore
        }

        // Only run on HTML content
        if (strpos($response->getHeaderLine('Content-Type'), 'html') === false) {
            return;
        }
    }

    /**
     * Records a visit, either adding a new row or
     * increasing the view count on an existing one.
     */
    final protected function record()
    {
        // Ignore CLI requests
        if (is_cli()) {
            return;
        }

        // Check for ignored AJAX requests
        if (service('request')->isAJAX() && $this->config->ignoreAjax) {
            return;
        }

        // Check if URI has been whitelisted from Visit check
        foreach ($this->config->excludeUris as $excluded) {
            if (url_is($excluded)) {
                return $this;
            }
        }

        $visits = model(VisitModel::class);
        $visit  = new Visit();

        // start the object with parsed URL components (https://secure.php.net/manual/en/function.parse-url.php)
        $visit->fill(parse_url(current_url()));

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