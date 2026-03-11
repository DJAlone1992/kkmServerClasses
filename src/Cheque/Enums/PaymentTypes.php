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
    public function getShortName()
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
}
