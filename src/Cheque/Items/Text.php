<?php

namespace Djalone\KkmServerClasses\Cheque\Items;
/**
 * Класс элемента текста.
 */
class Text extends Item
{
    /**
     * @var string
     */
    private string $text = '';
    /**
     * Размер шрифта (0–4).
     */
    private int $fontSize = 0;
    /**
     * Интенсивность текста (0–15).
    */
    private int $intensity = 0;

    /**
     * Конструктор элемента текста.
     *
     * @param string $text Содержимое текста (по умолчанию пустая строка).
     */
    public function __construct(string $text='')
    {
        $this->text = $text;
    }
    /**
     * Установить текст.
     *
     * @param string $text
     * @return static
     */
    public function setText(string $text)
    {
        $this->text = $text;
        return $this;
    }
    /**
     * Установить размер шрифта (0–4).
     *
     * @param int $fontSize
     * @return static
     */
    public function setFont(int $fontSize)
    {
        if ($fontSize < 0) {
            $fontSize = 0;
        }
        if ($fontSize > 4) {
            $fontSize = 4;
        }

        $this->fontSize = $fontSize;
        return $this;
    }

    /**
     * Установить интенсивность (0–15).
     *
     * @param int $intensity
     * @return static
     */
    public function setIntensity(int $intensity)
    {

        if ($intensity < 0) {
            $intensity = 0;
        }
        if ($intensity > 15) {
            $intensity = 15;
        }

        $this->intensity = $intensity;
        return $this;
    }

    /**
     * @return int Размер шрифта.
     */
    public function getFontSize(): int
    {
        return $this->fontSize;
    }

    /**
     * @return int Интенсивность текста.
     */
    public function getIntensity(): int
    {
        return $this->intensity;
    }

    /**
     * @return string Содержимое текста.
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Преобразовать текстовый элемент в массив для печати.
     *
     * @return array<string, array<string, string|int>>
     */
    public function toArray(): array
    {
        return [
            'PrintText' => [
                'Text' => $this->text,
                'Font' => $this->fontSize,
                'Intensity' => $this->intensity
            ]
        ];
    }
}
