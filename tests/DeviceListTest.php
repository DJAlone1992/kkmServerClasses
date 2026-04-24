<?php

declare(strict_types=1);

namespace Djalone\KkmServerClasses\Tests;

use Djalone\KkmServerClasses\DeviceList;
use PHPUnit\Framework\TestCase;

final class DeviceListTest extends TestCase
{
    public function test_device_list_returns_expected_payload(): void
    {
        $command = new DeviceList();

        $this->assertTrue($command->isValid());

        $realArray = $command->toRealArray();
        $this->assertSame('List', $realArray['Command']);
        $this->assertSame(true, $realArray['Active']);
        $this->assertSame(true, $realArray['OnOff']);
    }
}
