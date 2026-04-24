<?php

declare(strict_types=1);

namespace Djalone\KkmServerClasses\Tests\Cheque\Items;

use Djalone\KkmServerClasses\Cheque\Items\Image;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ImageTest extends TestCase
{
    public function test_set_image_and_get_image(): void
    {
        $image = new Image();
        $encoded = base64_encode('test-bytes');

        $image->setImage($encoded);

        $this->assertSame($encoded, $image->getImage());
        $this->assertSame([
            'PrintImage' => ['Image' => $encoded],
        ], $image->toArray());
    }

    public function test_constructor_converts_png_file_to_base64_bmp(): void
    {
        if (!extension_loaded('gd') || !function_exists('imagepng') || !function_exists('imagebmp') || !function_exists('imagecreatefrompng')) {
            $this->markTestSkipped('GD extension with PNG/BMP support is required for this test.');
        }

        $tmpFile = tempnam(sys_get_temp_dir(), 'imgtest');
        if ($tmpFile === false) {
            $this->fail('Unable to create temporary file for image test.');
        }

        $pngFile = $tmpFile . '.png';
        $gd = imagecreatetruecolor(8, 8);
        $white = imagecolorallocate($gd, 255, 255, 255);
        imagefilledrectangle($gd, 0, 0, 7, 7, $white);
        imagepng($gd, $pngFile);
        imagedestroy($gd);

        $image = new Image($pngFile);
        $base64Image = $image->getImage();

        $this->assertSame($base64Image, $image->toArray()['PrintImage']['Image']);
        $decoded = base64_decode($base64Image, true);
        $this->assertIsString($decoded);
        $this->assertStringStartsWith('BM', $decoded);

        @unlink($pngFile);
        @unlink($tmpFile);
    }

    public function test_constructor_throws_when_file_has_invalid_format(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'imgtest');
        if ($tmpFile === false) {
            $this->fail('Unable to create temporary file for invalid format test.');
        }

        $path = $tmpFile . '.txt';
        file_put_contents($path, 'not-an-image');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid file format');

        new Image($path);

        @unlink($path);
        @unlink($tmpFile);
    }
}
