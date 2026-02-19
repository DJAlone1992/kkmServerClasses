<?php

namespace Djalone\KkmServerClasses;

 class Helper{
public static function toFloat(int $value, int $precision):string{

        return number_format($value / 10 ** $precision, $precision, '.', '');

    }
}