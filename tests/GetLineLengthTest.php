<?php

declare(strict_types=1);

namespace Djalone\KkmServerClasses\Tests;

use Djalone\KkmServerClasses\GetLineLength;
use PHPUnit\Framework\TestCase;

final class GetLineLengthTest extends TestCase
{
    public function test_get_line_length_returns_empty_payload(): void
    {
        $command = new GetLineLength(
            'Иванов Иван',
            '123456789012',
            '1234567890',
            str_repeat('x', 40)
        );

        $this->assertSame([], $command->toArray());
        $this->assertSame('GetLineLength', $command->toRealArray()['Command']);
        $this->assertTrue($command->isValid());
    }

    public function test_get_line_length_inherits_base_validation(): void
    {
        $command = new GetLineLength('A', '1', '1234567890', 'short');

        $this->assertFalse($command->isValid());
        $this->assertCount(3, $command->getErrors());
    }
}
