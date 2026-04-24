<?php

namespace Djalone\KkmServerClasses\Cheque\Items;

use GdImage;
use RuntimeException;

/**
 * Класс элемента изображения.
 *
 * @todo Реализовать логику. Класс не готов к работе
 *
 */
class Image extends Item
{
    /**
     * @readonly
     */
    private string $image;

    /**
     * Конструктор элемента изображения.
     *
     * @param string $imagePath Путь к файлу изображения.
     */
    public function __construct(string $imagePath = '')
    {
        if (file_exists($imagePath)) {
            $this->convertImage($imagePath);
        }
    }
    private function convertImage(string $imagePath)
    {
        $ext = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
        $gd = match ($ext) {
            "jpg", "jpeg" => imagecreatefromjpeg($imagePath),
            "png" => imagecreatefrompng($imagePath),
            "gif" => imagecreatefromgif($imagePath),
            'webp' => imagecreatefromwebp($imagePath),
            'bmp' => imagecreatefrombmp($imagePath),
            default => throw new RuntimeException("Invalid file format"),
        };

        $this->imagetograyscale($gd);
        $xSize = imagesx($gd);
        $ySize = imagesy($gd);
        if ($xSize > 384 || $ySize > 384) {
            if ($xSize >= $ySize) {
                $xScale = 384;
            } else {
                $aspectRatio = $xSize / $ySize;
                $xScale = round(384 * $aspectRatio, 0, PHP_ROUND_HALF_DOWN);
            }
            $gd = imagescale($gd, $xScale);
        }
        $tempName = tempnam(sys_get_temp_dir(), 'img');
        imagebmp($gd, $tempName);
        $this->setImage(base64_encode(file_get_contents($tempName)));
    }

    private function imagetograyscale(GdImage $im)
    {
        if (imageistruecolor($im)) {
            imagetruecolortopalette($im, false, 256);
        }

        for ($c = 0; $c < imagecolorstotal($im); $c++) {
            $col = imagecolorsforindex($im, $c);
            $gray = round(0.299 * $col['red'] + 0.587 * $col['green'] + 0.114 * $col['blue']);
            imagecolorset($im, $c, $gray, $gray, $gray);
        }
    }

    /**
     * Установить base64-представление изображения.
     * @param string $imageBase64
     * @return static
     */
    public function setImage(string $imageBase64): static
    {
        $this->image = $imageBase64;
        return $this;
    }
    /**
     * Получить base64-представление изображения.
     *
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * Преобразовать изображение в массив для печати.
     *
     * @return array<string, array<string, string>>
     */
    public function toArray(): array
    {
        return [
            'PrintImage' => [
                'Image' => $this->image
            ]
        ];
    }
}
