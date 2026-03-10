<?php

namespace Djalone\KkmServerClasses\Cheque\Items;
/**
 * Класс элемента изображения.
 *
 * @todo Реализовать логику. Класс не готов к работе
 *
 */
class Image extends Item
{
    private readonly string $image;

    /**
     * Конструктор элемента изображения.
     *
     * @param string $image Путь к файлу изображения.
     */
    public function __construct($image)
    {
        if (file_exists($image)) {
            $buffer = file_get_contents($image);
        }
        $this->image = base64_encode($buffer);
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
