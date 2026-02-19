<?php

namespace Djalone\KkmServerClasses\Cheque\Items\Barcode;

use Djalone\KkmServerClasses\Cheque\Items\Item;

abstract class BarCode extends Item
{
    protected string $type;
    protected string $body;
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
