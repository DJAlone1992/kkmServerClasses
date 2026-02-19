<?php

namespace Djalone\KkmServerClasses\Cheque\Items\Barcode;

class BarCodeEAN13 extends BarCode
{

    public function __construct(string $barcode)
    {
        $this->type = 'EAN13';
        $this->body = $barcode;
        $this->checkData();
    }

    private function checkData()
    {
        if (strlen($this->body) !== 13) {
            throw new \InvalidArgumentException('Invalid barcode length');
        }
        if (!preg_match('/^[0-9]{13}$/', $this->body)) {
            throw new \InvalidArgumentException('Invalid barcode format');
        }
        $numbers = str_split((string) $this->body);
        $checkSum = 0;
        for ($i = 0; $i < 12; $i++) {
            if ($i % 2 == 0) {
                $checkSum += 3 * $numbers[$i];
            } else {
                $checkSum += $numbers[$i];
            }
        }
        $mod = $checkSum % 10;
        if ($mod == 0) {
            $checkDigit = 0;
        } else {
            $checkDigit = 10 - $mod;
        }
        if ($checkDigit != $numbers[12]) {
            throw new \Exception("Check digit does not match");
        }
    }
}
