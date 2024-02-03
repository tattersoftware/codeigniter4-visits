<?php

use Tatter\Visits\Entities\Visit;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class EntityTest extends TestCase
{
    /**
     * @dataProvider provideIpAddressCasts
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

    public static function provideIpAddressCasts(): iterable
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

    public function testIpAddressHandlesStrings(): void
    {
        $visit = new Visit();
        $visit->injectRawData([
            'ip_address' => '2130706433',
        ]);

        $result = $visit->getIpAddress();
        $this->assertSame('127.0.0.1', $result);

        $visit->injectRawData([
            'ip_address' => 'foo',
        ]);
        $this->assertNull($visit->getIpAddress());
    }
}
