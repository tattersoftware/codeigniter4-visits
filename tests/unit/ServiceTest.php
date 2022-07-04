<?php

use CodeIgniter\Test\CIUnitTestCase;
use Tatter\Visits\Visits;

/**
 * @internal
 */
final class ServiceTest extends CIUnitTestCase
{
    public function testServiceReturnsLibrary()
    {
        $result = service('visits');

        $this->assertInstanceOf(Visits::class, $result);
    }
}
