<?php

declare(strict_types=1);

namespace Djalone\KkmServerClasses\Tests;

use Djalone\KkmServerClasses\DepositingCash;
use PHPUnit\Framework\TestCase;

final class DepositingCashTest extends TestCase
{
	public function test_amount_set_get(): void
	{
		$cmd = new DepositingCash();
		$cmd->setAmount(1234);
		$this->assertSame(1234, $cmd->getAmount());
	}

	public function test_to_array_contains_amount(): void
	{
		$cmd = new DepositingCash();
		$cmd->setAmount(500);
		$arr = $cmd->toArray();
		$this->assertArrayHasKey('Amount', $arr);
		$this->assertSame('5.00', $arr['Amount']);
	}

	public function test_command_name_in_real_array(): void
	{
		$cmd = new DepositingCash();
		$real = $cmd->toRealArray();
		$this->assertSame('DepositingCash', $real['Command']);
	}
}
