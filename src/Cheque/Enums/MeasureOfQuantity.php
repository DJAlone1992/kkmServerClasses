<?php

namespace Djalone\KkmServerClasses\Cheque\Enums;

/**
 * Единицы измерения
 */
enum MeasureOfQuantity: int
{

    //шт.или ед.
    case UNITS = 0;
        //г
    case GRAMS = 10;
        //кг
    case KILOGRAMS = 11;
        //т
    case TON = 12;
        //см
    case CM = 20;
        //дм
    case DM = 21;
        //м
    case M = 22;
        //кв.см
    case CM_SQUARED = 30;
        //кв.дм
    case DM_SQUARED = 31;
        //кв.м
    case M_SQUARED = 32;
        //мл
    case MILLILITER = 40;
        //л
    case LITER = 41;
        //куб.м
    case M_CUBED = 42;
        //кВт ч
    case KILOWATT_HOURS = 50;
        //Гкал
    case GIGA_CALORIES = 51;
        //сутки
    case DAY = 70;
        //час
    case HOUR = 71;
        //мин
    case MINUTE = 72;
        //с
    case SECOND = 73;
        //Кбайт
    case KILOBYTE = 80;
        //Мбайт
    case MEGABYTE = 81;
        //Гбайт
    case GIGABYTE = 82;
        //Тбайт
    case TERABYTE = 83;
        //Прочее
    case OTHER = 255;

    public function getName(): string
    {
        return match ($this) {
            self::UNITS => 'Штука или единица',
            self::GRAMS => 'Грамм',
            self::KILOGRAMS => 'Килограмм',
            self::TON => 'Тонна',
            self::CM => 'Сантиметр',
            self::DM => 'Дециметр',
            self::M => 'Метр',
            self::CM_SQUARED => 'Квадратный сантиметр',
            self::DM_SQUARED => 'Квадратный дециметр',
            self::M_SQUARED => 'Квадратный метр',
            self::MILLILITER => 'Миллилитр',
            self::LITER => 'Литр',
            self::M_CUBED => 'Кубический метр',
            self::KILOWATT_HOURS => 'Киловатт-час',
            self::GIGA_CALORIES => 'Гигакалория',
            self::DAY => 'Сутки',
            self::HOUR => 'Час',
            self::MINUTE => 'Минута',
            self::SECOND => 'Секунда',
            self::KILOBYTE => 'Килобайт',
            self::MEGABYTE => 'Мегабайт',
            self::GIGABYTE => 'Гигабайт',
            self::TERABYTE => 'Терабайт',
            self::OTHER => 'Прочее',
            default => 'Не известно'
        };
    }
    public static function getArray(): array
	{

		$result = [];
		foreach (self::cases() as $value) {
			$result[$value->value] = $value->getName();
		}
		return $result;
	}
}
