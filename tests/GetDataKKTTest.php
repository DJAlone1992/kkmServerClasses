<?php

declare(strict_types=1);

namespace Djalone\KkmServerClasses\Tests;

use Djalone\KkmServerClasses\GetDataKKT;
use PHPUnit\Framework\TestCase;

final class GetDataKKTTest extends TestCase
{
	public function test_default_command(): void
	{
		$cmd = new GetDataKKT();
		$real = $cmd->toRealArray();
		$this->assertSame('GetDataKKT', $real['Command']);
	}
}
