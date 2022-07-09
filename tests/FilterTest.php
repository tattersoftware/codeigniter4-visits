<?php

use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FilterTestTrait;
use Tatter\Visits\Filters\VisitsFilter;
use Tests\Support\TestCase;
use Tests\Support\Transformers\BananaTransformer;

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

        $this->seeInDatabase('visits', ['path' => '/index.php']);
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
}
