<?php

namespace Djalone\KkmServerClasses\Cheque\Enums;

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
    public function getShortName(): string
    {
        switch ($this) {
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
}
