<?php

declare(strict_types=1);

namespace Djalone\KkmServerClasses\Tests\Cheque\Items;

use Djalone\KkmServerClasses\Cheque\Items\Image;
use PHPUnit\Framework\TestCase;

final class ImageTest extends TestCase
{
	private string $tmpFile;

	protected function setUp(): void
	{
		parent::setUp();
		// create a temporary image file
		$this->tmpFile = sys_get_temp_dir() . '/test-img.png';
		file_put_contents($this->tmpFile, 'dummy');
	}

	protected function tearDown(): void
	{
		if (file_exists($this->tmpFile)) {
			unlink($this->tmpFile);
		}
		parent::tearDown();
	}

	public function test_constructor_encodes_file(): void
	{
		$image = new Image($this->tmpFile);
		$this->assertIsString($image->getImage());
		$this->assertNotEmpty($image->getImage());

		$array = $image->toArray();
		$this->assertArrayHasKey('PrintImage', $array);
		$this->assertArrayHasKey('Image', $array['PrintImage']);
		$this->assertSame($image->getImage(), $array['PrintImage']['Image']);
	}
}
