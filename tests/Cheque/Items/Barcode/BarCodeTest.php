<?php

declare(strict_types=1);

namespace Djalone\KkmServerClasses\Tests\Cheque\Items\Barcode;

use Djalone\KkmServerClasses\Cheque\Items\Barcode\BarCodeCode39;
use Djalone\KkmServerClasses\Cheque\Items\Barcode\BarCodeEAN13;
use PHPUnit\Framework\TestCase;

final class BarCodeTest extends TestCase
{
	public function test_code39_basic(): void
	{
		$code = new BarCodeCode39('ABC123');
		$array = $code->toArray();
		$this->assertArrayHasKey('BarCode', $array);
		$this->assertSame('CODE39', $array['BarCode']['BarcodeType']);
		$this->assertSame('ABC123', $array['BarCode']['Barcode']);
	}

	public function test_ean13_valid(): void
	{
		// valid 13-digit barcode with correct checksum
		$code = new BarCodeEAN13('1234567890120');
		$array = $code->toArray();
		$this->assertSame('EAN13', $array['BarCode']['BarcodeType']);
		$this->assertSame('1234567890120', $array['BarCode']['Barcode']);
	}

	public function test_ean13_invalid_length(): void
	{
		$this->expectException(\InvalidArgumentException::class);
		new BarCodeEAN13('123');
	}

	public function test_ean13_invalid_format(): void
	{
		$this->expectException(\InvalidArgumentException::class);
		new BarCodeEAN13('abcdefghijklmn');
	}

	public function test_ean13_bad_checksum(): void
	{
		$this->expectException(\Exception::class);
		new BarCodeEAN13('4006381333930'); // last digit wrong
	}
}
