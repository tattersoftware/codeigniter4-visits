<?php

use CodeIgniter\Test\DatabaseTestTrait;
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
}
