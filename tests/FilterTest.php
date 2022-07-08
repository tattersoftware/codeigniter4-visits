<?php

use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FilterTestTrait;
use Tatter\Visits\Entities\Visit;
use Tatter\Visits\Filters\VisitsFilter;
use Tests\Support\TestCase;

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

    public function testFilterRecords(): void
    {
        $this->call();
        
        $this->seeInDatabase('visits', ['path' => '/index.php']);
    }
}
