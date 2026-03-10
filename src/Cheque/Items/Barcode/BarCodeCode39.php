<?php

namespace Djalone\KkmServerClasses\Cheque\Items\Barcode;

class BarCodeCode39 extends BarCode{


    /**
     * Конструктор штрих-кода CODE39.
     *
     * @param string $barcode Текст штрих-кода.
     */
    public function __construct(string $barcode='')
    {
        $this->type='CODE39';
        $this->body=$barcode;
    }

}