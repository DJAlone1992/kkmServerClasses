<?php

declare(strict_types=1);

namespace Djalone\KkmServerClasses\Tests\Cheque\Items;

use Djalone\KkmServerClasses\Cheque\Enums\BarCodeType;
use Djalone\KkmServerClasses\Cheque\Items\BarCode;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class BarCodeTest extends TestCase
{
    public function test_constructor_sets_type_and_body(): void
    {
        $barcode = new BarCode('hello-world', BarCodeType::QR);

        $this->assertSame(BarCodeType::QR, $barcode->getType());
        $this->assertSame('hello-world', $barcode->getBody());
    }

    public function test_constructor_accepts_string_type(): void
    {
        $barcode = new BarCode('', 'EAN13');

        $this->assertSame(BarCodeType::EAN13, $barcode->getType());
        $this->assertSame('', $barcode->getBody());
    }

    public function test_to_array_contains_barcode_type_and_body(): void
    {
        $barcode = new BarCode('1234567890123', BarCodeType::QR);
        $result = $barcode->toArray();

        $this->assertSame([
            'BarCode' => [
                'BarcodeType' => 'QR',
                'Barcode' => '1234567890123',
            ],
        ], $result);
    }

    public function test_valid_ean13_barcode_is_accepted(): void
    {
        $barcode = new BarCode('4006381333931', BarCodeType::EAN13);

        $this->assertSame(BarCodeType::EAN13, $barcode->getType());
        $this->assertSame('4006381333931', $barcode->getBody());
    }

    public function test_ean13_barcode_with_invalid_length_throws_exception(): void
    {
        $barcode = new BarCode('', BarCodeType::EAN13);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid barcode length');

        $barcode->setBody('123456789012');
    }

    public function test_ean13_barcode_with_invalid_format_throws_exception(): void
    {
        $barcode = new BarCode('', BarCodeType::EAN13);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid barcode format');

        $barcode->setBody('12345678901A3');
    }

    public function test_ean13_barcode_with_invalid_check_digit_throws_exception(): void
    {
        $barcode = new BarCode('', BarCodeType::EAN13);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Check digit does not match');

        $barcode->setBody('4006381333930');
    }
}
