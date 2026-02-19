<?php

namespace Djalone\KkmServerClasses\Cheque\Items\Barcode;

class BarCodeCode39 extends BarCode{

public function __construct(string $barcode)
{
    $this->type='CODE39';
    $this->body=$barcode;
}
}