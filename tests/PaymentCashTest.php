<?php

declare(strict_types=1);

namespace Djalone\KkmServerClasses\Tests;

use Djalone\KkmServerClasses\PaymentCash;
use PHPUnit\Framework\TestCase;

final class PaymentCashTest extends TestCase
{
	public function test_amount_set_get(): void
	{
		$cmd = new PaymentCash();
		$cmd->setAmount(777);
		$this->assertSame(777, $cmd->getAmount());
	}

	public function test_to_array_contains_amount(): void
	{
		$cmd = new PaymentCash();
		$cmd->setAmount(2500);
		$arr = $cmd->toArray();
		$this->assertArrayHasKey('Amount', $arr);
		$this->assertSame('25.00', $arr['Amount']);
	}

	public function test_command_name_in_real_array(): void
	{
		$cmd = new PaymentCash();
		$real = $cmd->toRealArray();
		$this->assertSame('PaymentCash', $real['Command']);
	}
}
