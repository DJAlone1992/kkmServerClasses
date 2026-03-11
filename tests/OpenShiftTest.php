<?php

declare(strict_types=1);

namespace Djalone\KkmServerClasses\Tests;

use Djalone\KkmServerClasses\OpenShift;
use PHPUnit\Framework\TestCase;

final class OpenShiftTest extends TestCase
{
	public function test_default_command(): void
	{
		$cmd = new OpenShift();
		$real = $cmd->toRealArray();
		$this->assertSame('OpenShift', $real['Command']);
	}
}
