<?php

namespace Djalone\KkmServerClasses\Cheque\Enums;

use ReflectionClass;

/**
 * Признак способа расчета. Тег ОФД 1214. Для ФФД.1.05 и выше обязательное поле
 */
class SignMethodCalculation
{
	// 1: "ПРЕДОПЛАТА 100% (Полная предварительная оплата до момента передачи предмета расчета)"
	public const FULL_PREPAYMENT = 1;
	// 2: "ПРЕДОПЛАТА (Частичная предварительная оплата до момента передачи предмета расчета)"
	public const PREPAYMENT = 2;
	// 3: "АВАНС"
	public const AVANCE = 3;
	// 4: "ПОЛНЫЙ РАСЧЕТ (Полная оплата, в том числе с учетом аванса в момент передачи предмета расчета)"
	public const FULL_PAYMENT = 4;
	// 5: "ЧАСТИЧНЫЙ РАСЧЕТ И КРЕДИТ (Частичная оплата предмета расчета в момент его передачи с последующей оплатой в кредит )"
	public const PARTIAL_PAYMENT = 5;
	// 6: "ПЕРЕДАЧА В КРЕДИТ (Передача предмета расчета без его оплаты в момент его передачи с последующей оплатой в кредит)"
	public const TO_CREDIT = 6;
	// 7: "ОПЛАТА КРЕДИТА (Оплата предмета расчета после его передачи с оплатой в кредит )"
	public const FROM_CREDIT = 7;

	public static function getName($value): string
	{
		switch ($value) {
			case self::FULL_PAYMENT:
				return 'Предоплата 100%';
			case self::PREPAYMENT:
				return 'Предоплата';
			case self::AVANCE:
				return 'Аванс';
			case self::FULL_PAYMENT:
				return 'Полный расчет';
			case self::PARTIAL_PAYMENT:
				return 'Частичный расчет';
			case self::TO_CREDIT:
				return 'Передача в кредит';
			case self::FROM_CREDIT:
				return 'Оплата кредита';
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
