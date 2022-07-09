<?php

use Tatter\Visits\Entities\Visit;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class EntityTest extends TestCase
{
    /**
     * @dataProvider ipAddressProvider
     *
     * @param mixed $input
     */
    public function testIpAddressCasts($input, ?int $stored, ?string $retrieved): void
    {
        $visit            = new Visit();
        $visit->ipAddress = $input;

        $result = $visit->toRawArray()['ip_address'];
        $this->assertSame($stored, $result);

        $result = $visit->getIpAddress();
        $this->assertSame($retrieved, $result);
    }

    public function ipAddressProvider(): array
    {
        return [
            ['127.0.0.1', 2_130_706_433, '127.0.0.1'],
            [2_130_706_433, 2_130_706_433, '127.0.0.1'],
            [0, 0, '0.0.0.0'],
            ['-55.0.0.1', null, null],
            [0.1, null, null],
            [PHP_INT_MAX + 1, null, null],
            [null, null, null],
        ];
    }
}
