<?php

declare(strict_types=1);

namespace Djalone\KkmServerClasses\Tests;

use Djalone\KkmServerClasses\XReport;
use PHPUnit\Framework\TestCase;

final class XReportTest extends TestCase
{
	public function test_default_command(): void
	{
		$cmd = new XReport();
		$real = $cmd->toRealArray();
		$this->assertSame('XReport', $real['Command']);
	}
}
