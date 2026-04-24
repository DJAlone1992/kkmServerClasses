<?php

declare(strict_types=1);

namespace Djalone\KkmServerClasses\Tests;

use Djalone\KkmServerClasses\GetDataCheck;
use PHPUnit\Framework\TestCase;

final class GetDataCheckTest extends TestCase
{
    public function test_get_data_check_payload_and_accessors(): void
    {
        $command = new GetDataCheck(
            'Иванов Иван',
            '123456789012',
            '1234567890',
            str_repeat('x', 40)
        );

        $command
            ->setFiscalNumber(123)
            ->setNumberOfCopies(2);

        $this->assertSame(123, $command->getFiscalNumber());
        $this->assertSame(2, $command->getNumberOfCopies());
        $this->assertTrue($command->isValid());

        $realArray = $command->toRealArray();
        $this->assertSame('GetDataCheck', $realArray['Command']);
        $this->assertSame(123, $realArray['FiscalNumber']);
        $this->assertSame(2, $realArray['NumberCopies']);
    }
}
