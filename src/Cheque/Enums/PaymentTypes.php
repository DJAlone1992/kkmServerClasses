<?php

namespace Djalone\KkmServerClasses\Cheque\Enums;
/**
 * Тип оплаты.
 * Виртуальный тип, для механизма отправки.
 */
enum PaymentTypes: int
{
	case Cash = 1;
	case Electronic = 2;
	case Advanced = 3;
	case Credit = 4;
	case CashProvision = 5;

	public function getShortName(): string
	{
		return match ($this) {
			self::Cash => 'Нал',
			self::Electronic => 'Безнал',
			self::Advanced => 'Предоплата',
			self::Credit => 'Из кредита',
			self::CashProvision => 'В кредит',
		};
	}

	public static function getArray(): array
	{

		$result = [];
		foreach (self::cases() as $value) {
			$result[$value->value] = $value->getShortName();
		}
		return $result;
	}
}
