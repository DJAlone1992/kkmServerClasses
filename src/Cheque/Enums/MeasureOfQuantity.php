<?php

namespace Djalone\KkmServerClasses\Cheque\Enums;

use ReflectionClass;

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

    public static function getName($value): string
    {
        switch ($value) {
            case self::UNITS:
                return 'Штука или единица';
            case self::GRAMS:
                return 'Грамм';
            case self::KILOGRAMS:
                return 'Килограмм';
            case self::TON:
                return 'Тонна';
            case self::CM:
                return 'Сантиметр';
            case self::DM:
                return 'Дециметр';
            case self::M:
                return 'Метр';
            case self::CM_SQUARED:
                return 'Квадратный сантиметр';
            case self::DM_SQUARED:
                return 'Квадратный дециметр';
            case self::M_SQUARED:
                return 'Квадратный метр';
            case self::MILLILITER:
                return 'Миллилитр';
            case self::LITER:
                return 'Литр';
            case self::M_CUBED:
                return 'Кубический метр';
            case self::KILOWATT_HOURS:
                return 'Киловатт-час';
            case self::GIGA_CALORIES:
                return 'Гигакалория';
            case self::DAY:
                return 'Сутки';
            case self::HOUR:
                return 'Час';
            case self::MINUTE:
                return 'Минута';
            case self::SECOND:
                return 'Секунда';
            case self::KILOBYTE:
                return 'Килобайт';
            case self::MEGABYTE:
                return 'Мегабайт';
            case self::GIGABYTE:
                return 'Гигабайт';
            case self::TERABYTE:
                return 'Терабайт';
            case self::OTHER:
                return 'Прочее';
            default:
                return 'Не известно';
        }
    }
   public static function getArray(): array
	{
		$reflection = new ReflectionClass(self::class);
		$cases = $reflection->getConstants();
		$result = [];
		foreach ($cases as $value) {
			$result[$value] = self::getName($value);
		}
		return $result;
	}
}
