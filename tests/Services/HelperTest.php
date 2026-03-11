<?php

declare(strict_types=1);

namespace Djalone\KkmServerClasses\Tests\Services;

use Djalone\KkmServerClasses\Services\Helper;
use PHPUnit\Framework\TestCase;

final class HelperTest extends TestCase
{
    public function test_toFloat_basic_conversion_with_precision_2(): void
    {
        $this->assertSame('1.23', Helper::toFloat(123, 2));
        $this->assertSame('0.00', Helper::toFloat(0, 2));
    }

    public function test_toFloat_handles_large_values_precisely(): void
    {
        $this->assertSame('1234567890.12', Helper::toFloat(123456789012, 2));
    }

    public function test_toFloat_precision_0_returns_integer_string(): void
    {
        $this->assertSame('123', Helper::toFloat(123, 0));
        $this->assertSame('0', Helper::toFloat(0, 0));
    }

    public function test_toFloat_negative_values_are_supported(): void
    {
        $this->assertSame('-1.23', Helper::toFloat(-123, 2));
        $this->assertSame('-0.12', Helper::toFloat(-12, 2));
        $this->assertSame('-0.01', Helper::toFloat(-1, 2));
    }

    public function test_toFloat_high_precision_padding_and_decimal_point(): void
    {
        $this->assertSame('0.00001', Helper::toFloat(1, 5));
        $this->assertSame('10.00000', Helper::toFloat(1000000, 5));
    }
}
