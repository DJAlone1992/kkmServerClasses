<?php

declare(strict_types=1);

namespace Djalone\KkmServerClasses\Tests\Cheque\Items;

use Djalone\KkmServerClasses\Cheque\Items\Text;
use PHPUnit\Framework\TestCase;

final class TextTest extends TestCase
{
    public function test_default_values_and_constructor(): void
    {
        $text = new Text('hello');
        $this->assertSame('hello', $text->getText());
        $this->assertSame(0, $text->getFontSize());
        $this->assertSame(0, $text->getIntensity());
    }

    public function test_setters_and_getters(): void
    {
        $text = new Text('world');
        $text->setFont(3)->setIntensity(10)->setText('foo');

        $this->assertSame('foo', $text->getText());
        $this->assertSame(3, $text->getFontSize());
        $this->assertSame(10, $text->getIntensity());
    }

    public function test_font_bounds_are_enforced(): void
    {
        $text = new Text('')->setFont(-5);
        $this->assertSame(0, $text->getFontSize());
        $text->setFont(10);
        $this->assertSame(4, $text->getFontSize());
    }

    public function test_intensity_bounds_are_enforced(): void
    {
        $text = new Text('')->setIntensity(-1);
        $this->assertSame(0, $text->getIntensity());
        $text->setIntensity(100);
        $this->assertSame(15, $text->getIntensity());
    }

    public function test_to_array_structure(): void
    {
        $text = new Text('abc')->setFont(2)->setIntensity(5);
        $array = $text->toArray();

        $this->assertArrayHasKey('PrintText', $array);
        $this->assertArrayHasKey('Text', $array['PrintText']);
        $this->assertArrayHasKey('Font', $array['PrintText']);
        $this->assertArrayHasKey('Intensity', $array['PrintText']);
        $this->assertSame('abc', $array['PrintText']['Text']);
        $this->assertSame(2, $array['PrintText']['Font']);
        $this->assertSame(5, $array['PrintText']['Intensity']);
    }
}
