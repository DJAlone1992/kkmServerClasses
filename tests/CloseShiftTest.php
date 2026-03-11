<?php

declare(strict_types=1);

namespace Djalone\KkmServerClasses\Tests;

use Djalone\KkmServerClasses\CloseShift;
use PHPUnit\Framework\TestCase;

final class CloseShiftTest extends TestCase
{
	public function test_default_command(): void
	{
		$cmd = new CloseShift();
		$real = $cmd->toRealArray();
		$this->assertSame('CloseShift', $real['Command']);
	}
}
