<?php

namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
abstract class TestCase extends CIUnitTestCase
{
    /**
     * The namespace(s) to help us find the migration classes.
     * Empty is equivalent to running `spark migrate -all`.
     *
     * @var array|string|null
     */
    protected $namespace;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        helper(['auth']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->resetServices();
    }
}
