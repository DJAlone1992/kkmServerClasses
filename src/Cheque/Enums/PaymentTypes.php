<?php

namespace Djalone\KkmServerClasses\Cheque\Enums;

use ReflectionClass;

/**
 * Тип оплаты.
 * Виртуальный тип, для механизма отправки.
 */
class PaymentTypes
{
    public const Cash = 1;
    public const Electronic = 2;
    public const Advanced = 3;
    public const Credit = 4;
    public const CashProvision = 5;
    public static function getShortName($value): string
    {
        switch ($value) {
            case self::Cash:
                return 'Нал';
            case self::Electronic:
                return 'Безнал';
            case self::Advanced:
                return 'Предоплата';
            case self::Credit:
                return 'Из кредита';
            case self::CashProvision:
                return 'В кредит';
            default:
                return 'Не известно';
        }
    }

    public static function tryFrom(?int $value): ?int
    {
        if (is_null($value)) {
            return null;
        }
        switch ($value) {
            case self::Cash:
                return self::Cash;
            case self::Electronic:
                return self::Electronic;
            case self::Advanced:
                return self::Advanced;
            case self::Credit:
                return self::Credit;
            case self::CashProvision:
                return self::CashProvision;
            default:
                return null;
        }
    }

    public static function getArray(): array
    {
        $reflection = new ReflectionClass(self::class);
        $cases = $reflection->getConstants();
        $result = [];
        foreach ($cases as $value) {
            $result[$value] = self::getShortName($value);
        }
        return $result;
    }
}
