<?php

namespace Djalone\KkmServerClasses\Cheque\Items\Barcode;

use Djalone\KkmServerClasses\Cheque\Items\Item;
/**
 * Элемент чека - штрихкод.
 */
abstract class BarCode extends Item
{
    protected string $type;
    protected string $body;

    /**
     * Получить тип штрих-кода.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Получить содержимое штрих-кода.
     *
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Установить тип штрих-кода.
     *
     * @param string $type
     * @return static
     */
    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Установить содержимое штрих-кода.
     *
     * @param string $body
     * @return static
     */
    public function setBody(string $body): static
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Преобразовать штрихкод в ассоциативный массив.
     *
     * @return array<string, array<string, string>>
     */
    public function toArray(): array
    {
        return [
            'BarCode' => [
                'BarcodeType' => $this->type,
                'Barcode' => $this->body
            ]
        ];
    }
}
