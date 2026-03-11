<?php

namespace Djalone\KkmServerClasses\Cheque\Enums;

/**
 * Единицы измерения
 */
class MeasureOfQuantity
{

    //шт.или ед.
    public const UNITS = 0;
        //г
    public const GRAMS = 10;
        //кг
    public const KILOGRAMS = 11;
        //т
    public const TON = 12;
        //см
    public const CM = 20;
        //дм
    public const DM = 21;
        //м
    public const M = 22;
        //кв.см
    public const CM_SQUARED = 30;
        //кв.дм
    public const DM_SQUARED = 31;
        //кв.м
    public const M_SQUARED = 32;
        //мл
    public const MILLILITER = 40;
        //л
    public const LITER = 41;
        //куб.м
    public const M_CUBED = 42;
        //кВт ч
    public const KILOWATT_HOURS = 50;
        //Гкал
    public const GIGA_CALORIES = 51;
        //сутки
    public const DAY = 70;
        //час
    public const HOUR = 71;
        //мин
    public const MINUTE = 72;
        //с
    public const SECOND = 73;
        //Кбайт
    public const KILOBYTE = 80;
        //Мбайт
    public const MEGABYTE = 81;
        //Гбайт
    public const GIGABYTE = 82;
        //Тбайт
    public const TERABYTE = 83;
        //Прочее
    public const OTHER = 255;
}
