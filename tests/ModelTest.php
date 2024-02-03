<?php

use CodeIgniter\Config\Services;
use CodeIgniter\Test\DatabaseTestTrait;
use Tatter\Visits\Entities\Visit;
use Tatter\Visits\Models\VisitModel;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class ModelTest extends TestCase
{
    use DatabaseTestTrait;

    private VisitModel $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new VisitModel();
    }

    /**
     * @dataProvider findSimilarNullProvider
     */
    public function testFindSimilarNull(string $field): void
    {
        if (in_array($field, ['ip_address', 'session_id', 'user_id'], true)) {
            config('Visits')->trackingMethod = $field;
        }

        $visit           = fake(VisitModel::class);
        $visit->{$field} = null;

        $result = $this->model->findSimilar($visit);

        $this->assertNull($result);
    }

    public function findSimilarNullProvider(): array
    {
        return [
            ['host'],
            ['path'],
            ['ip_address'],
            ['session_id'],
            ['user_id'],
        ];
    }

    public function testFindSimilarNotFound(): void
    {
        $visit  = fake(VisitModel::class, [], false); // do not add it to the database
        $result = $this->model->findSimilar($visit);

        $this->assertNull($result);
    }

    public function testMakeFromRequest(): void
    {
        $request  = service('request');
        $expected = [
            'scheme'     => 'https',
            'host'       => 'example.com',
            'port'       => '',
            'user'       => '',
            'pass'       => '',
            'path'       => '/index.php/',
            'query'      => '',
            'fragment'   => '',
            'session_id' => session_id(),
            'user_id'    => null,
            'user_agent' => '',
            'ip_address' => null,
        ];

        $result = $this->model->makeFromRequest($request);
        $this->assertInstanceOf(Visit::class, $result);

        foreach ($expected as $key => $value) {
            $this->assertSame($value, $result->{$key}, "{$key} was not the expected value");
        }
    }

    public function testMakeFromRequestUsesUserId(): void
    {
        service('auth')->login(42);

        $result = $this->model->makeFromRequest(service('request'));

        $this->assertInstanceOf(Visit::class, $result);
        $this->assertSame(42, $result->user_id);
    }

/*
    Temporarily disabled because config injection isn't working anymore

    public function testMakeFromRequestRespectsBaseUrl(): void
    {
        $config = config('App');

        $config->baseURL   = 'http://foo.bar/folder/';
        $config->indexPage = '';

        $request = service('request', $config)->setPath('fruits/banana#ripe');

        $result = $this->model->makeFromRequest($request);

        $this->assertSame('http', $result->scheme);
        $this->assertSame('foo.bar', $result->host);
        $this->assertSame('/folder/fruits/banana', $result->path);
        $this->assertSame('ripe', $result->fragment);
    }
*/

    public function testMakeFromRequestIgnoresPass(): void
    {
        config('App')->baseURL = 'https://banana:ananab@example.com/';

        $result = $this->model->makeFromRequest(service('request'));

        $this->assertSame('', $result->pass);
    }

    public function testMakeFromRequestConvertsIp(): void
    {
        $_SERVER['REMOTE_ADDR'] = $expected = '192.168.0.1';

        $result = $this->model->makeFromRequest(single_service('request'));

        $this->assertSame($expected, $result->ipAddress);
        $this->assertSame(ip2long($expected), $result->toRawArray()['ip_address']);

        unset($_SERVER['REMOTE_ADDR']);
    }

    /**
     * @see https://github.com/tattersoftware/codeigniter4-visits/issues/23
     */
    public function testAllowsLongSessionIds(): void
    {
        $expected = 'Who shall call them from the grey twilight the forgotten people The heir of him to whom the oath they swore';
        fake(VisitModel::class, ['session_id' => $expected]);

        $result = $this->model->first();

        $this->assertInstanceOf(Visit::class, $result);
        $this->assertSame($expected, $result->session_id);
    }
}
