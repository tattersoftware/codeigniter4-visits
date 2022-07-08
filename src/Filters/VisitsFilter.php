<?php

namespace Tatter\Visits\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use RuntimeException;
use Tatter\Visits\Config\Visits;
use Tatter\Visits\Entities\Visit;
use Tatter\Visits\Models\VisitModel;

/**
 * Visits Filter
 *
 * Records visits for matching routes.
 */
class VisitsFilter implements FilterInterface
{
    protected Visits $config;
    protected VisitModel $model;

    public function __construct()
    {
        $this->config = config('Visits');
        $this->model  = model(VisitModel::class);
    }

    public function before(RequestInterface $request, $arguments = null): void
    {
        $this->record($request);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
    {
        // Ignoring redirects
        if ($this->config->ignoreRedirects && $response instanceof RedirectResponse) {
            return;
        }
        // Ignore empty responses
        if ($this->config->requireBody && empty($response->getBody())) {
            return;
        }
        // Ignore non-HTML response
        if ($this->config->requireHtml && strpos($response->getHeaderLine('Content-Type'), 'html') === false) {
            return;
        }

        $this->record($request);
    }

    /**
     * Records a visit, either adding a new row or
     * increasing the view count on an existing one.
     *
     * @throws RuntimeException
     */
    final protected function record(RequestInterface $request): void
    {
        if (! $request instanceof IncomingRequest) {
            throw new RuntimeException(static::class . ' requires an IncomingRequest object.');
        }

        if (is_cli() && ENVIRONMENT !== 'testing') {
            return; // @codeCoverageIgnore
        }

        // Verify helper function from codeigniter4/authentication-implementation
        if (! function_exists('user_id')) {
            throw new RuntimeException('The necessary user_id() function was not found! Did you forget to preload your helper?');
        }

        $visit = $this->model->makeFromRequest($request);

        // Check for an existing similar record
        if ($similar = $this->model->findSimilar($visit)) {
            // increment number of views and update
            $similar->views++;
            $this->model->save($similar);

            return;
        }

        // Create a new visit record
        if ($this->model->save($visit)) {
            return;
        }

        $error = implode(' ', $this->model->errors());
        throw new RuntimeException('Failed to create visit record: ' . $error);
    }
}
