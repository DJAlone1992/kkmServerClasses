<?php

namespace Djalone\KkmServerClasses\Cheque\Items;

use Djalone\KkmServerClasses\Cheque\Enums\BarCodeType;
use Djalone\KkmServerClasses\Cheque\Items\Item;
use Exception;
use InvalidArgumentException;
use RuntimeException;

/**
 * Элемент чека - штрихкод.
 */
class BarCode extends Item
{
    private string $type;
    private string $body;
    /**
     * Конструктор штрих-кода с проверкой контрольной цифры.
     *
     * @param string $barcode текст штрих-кода
     * @param BarCodeType|string $type тип штрих-кода
     * @throws \InvalidArgumentException при неверной длине или формате.
     * @throws \Exception при несовпадении контрольной цифры.
     */
    public function __construct(string $barcode = '', $type = BarCodeType::QR)
    {
        $this->setType($type);
        $this->setBody($barcode);
    }
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
    public function setType($type)
    {
        $type = BarCodeType::tryFrom($type);
        $this->type = $type;
        return $this;
    }

    /**
     * Установить содержимое штрих-кода.
     *
     * @param string $body
     * @return static
     */
    public function setBody(string $body)
    {
        if (!$this->type) {
            throw new RuntimeException('Barcode type is not set');
        }
        if (strlen($body)) {
            $this->body = $body;
            $this->checkData();
        }
        $this->body = $body;
        return $this;
    }
    /**
     * Проверка данных штрих-кода
     * @throws InvalidArgumentException
     * @throws Exception
     * @return void
     */
    private function checkData(): void
    {
        if ($this->type !== BarCodeType::EAN13) {
            return;
        }
        if (strlen($this->body) !== 13) {
            throw new InvalidArgumentException('Invalid barcode length');
        }
        if (!preg_match('/^[0-9]{13}$/', $this->body)) {
            throw new InvalidArgumentException('Invalid barcode format');
        }
        $numbers = str_split((string) $this->body);
        $checkSum = 0;
        for ($i = 0; $i < 12; $i++) {
            if ($i % 2 == 0) {
                $checkSum +=  $numbers[$i];
            } else {
                $checkSum += 3 * $numbers[$i];
            }
        }
        $mod = $checkSum % 10;
        if ($mod == 0) {
            $checkDigit = 0;
        } else {
            $checkDigit = 10 - $mod;
        }
        if ($checkDigit != $numbers[12]) {
            throw new Exception("Check digit does not match");
        }
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
