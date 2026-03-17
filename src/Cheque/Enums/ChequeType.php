<?php

namespace Djalone\KkmServerClasses\Cheque\Enums;

use ReflectionClass;

/**
 * Тип чека, Тег 1054;
 */
class ChequeType
{
    // 0 – продажа/приход;
    public const INCOME = 0;
    // 1 – возврат продажи/прихода;
    public const INCOME_RETURN = 1;
    // 2 – корректировка продажи/прихода;
    public const INCOME_CORRECTION = 2;
    // 3 – корректировка возврата продажи/прихода; (>=ФФД 1.1)
    public const INCOME_RETURN_CORRECTION = 3;
    // 10 – покупка/расход;
    public const OUTCOME = 10;
    // 11 - возврат покупки/расхода;
    public const OUTCOME_RETURN = 11;
    // 12 – корректировка покупки/расхода;
    public const OUTCOME_CORRECTION = 12;
    // 13 – корректировка возврата покупки/расхода; (>=ФФД 1.1)
    public const OUTCOME_RETURN_CORRECTION = 13;

    public static function getName($value): string
    {
        switch ($value) {
            case self::INCOME:
                return 'Продажа/приход';
            case self::INCOME_RETURN:
                return 'Возврат продажи/прихода';
            case self::INCOME_CORRECTION:
                return 'Корректировка продажи/прихода';
            case self::INCOME_RETURN_CORRECTION:
                return 'Корректировка возврата продажи/прихода';
            case self::OUTCOME:
                return 'Покупка/расход';
            case self::OUTCOME_RETURN:
                return 'Возврат покупки/расхода';
            case self::OUTCOME_CORRECTION:
                return 'Корректировка покупки/расхода';
            case self::OUTCOME_RETURN_CORRECTION:
                return 'Корректировка возврата покупки/расхода';
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
