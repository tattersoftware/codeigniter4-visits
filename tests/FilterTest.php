<?php

use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FilterTestTrait;
use Tatter\Visits\Filters\VisitsFilter;
use Tests\Support\TestCase;
use Tests\Support\Transformers\BananaTransformer;
use Tests\Support\Transformers\DecepticonTransformer;

/**
 * @internal
 */
final class FilterTest extends TestCase
{
    use DatabaseTestTrait;
    use FilterTestTrait;

    protected function call(string $position = 'after'): void
    {
        $this->getFilterCaller(VisitsFilter::class, $position)();
    }

    public function testRecords(): void
    {
        $this->call();

        $this->seeInDatabase('visits', ['path' => '/index.php/']);
    }

    public function testIpAddressIncrements(): void
    {
        config('Visits')->trackingMethod = 'ip_address';
        $_SERVER['REMOTE_ADDR']          = '192.168.0.1';
        $this->request                   = single_service('request');

        $this->call();
        $this->call();

        $this->seeInDatabase('visits', ['views' => 2]);

        unset($_SERVER['REMOTE_ADDR']);
    }

    public function testUserIdIncrements(): void
    {
        config('Visits')->trackingMethod = 'user_id';
        service('auth')->login(42);

        $this->call();
        $this->call();

        $this->seeInDatabase('visits', ['views' => 2]);
    }

    /**
     * @runInSeparateProcess
     */
    public function testSessionIdIncrements(): void
    {
        config('Visits')->trackingMethod = 'session_id';
        session_id('abc123');
        $this->call();
        $this->call();

        $this->seeInDatabase('visits', ['views' => 2]);
    }

    public function testBeforeRecords(): void
    {
        $this->call('before');

        $this->seeNumRecords(1, 'visits', []);
    }

    public function testIgnoresAjax(): void
    {
        config('Visits')->ignoreAjax = true;
        $this->request->setHeader('X-Requested-With', 'xmlhttprequest');

        $this->call();

        $this->seeNumRecords(0, 'visits', []);
    }

    public function testNotIgnoresAjax(): void
    {
        config('Visits')->ignoreAjax = false;
        $this->request->setHeader('X-Requested-With', 'xmlhttprequest');

        $this->call();

        $this->seeNumRecords(1, 'visits', []);
    }

    public function testIgnoresRedirects(): void
    {
        config('Visits')->ignoreRedirects = true;
        $this->response                   = redirect();

        $this->call();

        $this->seeNumRecords(0, 'visits', []);
    }

    public function testNotIgnoresRedirects(): void
    {
        config('Visits')->ignoreRedirects = false;
        $this->response                   = redirect();

        $this->call();

        $this->seeNumRecords(1, 'visits', []);
    }

    public function testRequiresBody(): void
    {
        config('Visits')->requireBody = true;
        $this->response->setBody('');

        $this->call();

        $this->seeNumRecords(0, 'visits', []);
    }

    public function testNotRequiresBody(): void
    {
        config('Visits')->requireBody = false;
        $this->response->setBody('');

        $this->call();

        $this->seeNumRecords(1, 'visits', []);
    }

    public function testRequiresHtml(): void
    {
        config('Visits')->requireHtml = true;
        $this->response->setContentType('application/json');

        $this->call();

        $this->seeNumRecords(0, 'visits', []);
    }

    public function testNotRequiresHtml(): void
    {
        config('Visits')->requireHtml = false;
        $this->response->setContentType('application/json');

        $this->call();

        $this->seeNumRecords(1, 'visits', []);
    }

    public function testAppliesTransformers(): void
    {
        config('Visits')->transformers = [BananaTransformer::class];

        $this->call();

        $this->seeInDatabase('visits', [
            'host'  => 'banana',
            'query' => 'banana',
        ]);
    }

    public function testShortCircuitsTransformers(): void
    {
        config('Visits')->transformers = [
            DecepticonTransformer::class,
            BananaTransformer::class,
        ];

        $this->call();

        $this->seeNumRecords(0, 'visits', []);
    }

/*
    Temporarily disabled because request handling doesn't work like this anymore

    public function testRequiresIncomingRequest(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(VisitsFilter::class . ' requires an IncomingRequest object.');

        $this->request = service('clirequest');

        $this->call();
    }

    // Temporarily disabled because config injection isn't working anymore
    public function testRequiresValidVisit(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to create visit record: The host field is required.');

        config('App')->baseURL = '0';

        $this->call();
    }
*/
}
