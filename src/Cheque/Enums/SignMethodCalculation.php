<?php

namespace Djalone\KkmServerClasses\Cheque\Enums;

/**
 * Признак способа расчета. Тег ОФД 1214. Для ФФД.1.05 и выше обязательное поле
 */
enum SignMethodCalculation: int
{
	// 1: "ПРЕДОПЛАТА 100% (Полная предварительная оплата до момента передачи предмета расчета)"
	case FULL_PREPAYMENT = 1;
		// 2: "ПРЕДОПЛАТА (Частичная предварительная оплата до момента передачи предмета расчета)"
	case PREPAYMENT = 2;
		// 3: "АВАНС"
	case AVANCE = 3;
		// 4: "ПОЛНЫЙ РАСЧЕТ (Полная оплата, в том числе с учетом аванса в момент передачи предмета расчета)"
	case FULL_PAYMENT = 4;
		// 5: "ЧАСТИЧНЫЙ РАСЧЕТ И КРЕДИТ (Частичная оплата предмета расчета в момент его передачи с последующей оплатой в кредит )"
	case PARTIAL_PAYMENT = 5;
		// 6: "ПЕРЕДАЧА В КРЕДИТ (Передача предмета расчета без его оплаты в момент его передачи с последующей оплатой в кредит)"
	case TO_CREDIT = 6;
		// 7: "ОПЛАТА КРЕДИТА (Оплата предмета расчета после его передачи с оплатой в кредит )"
	case FROM_CREDIT = 7;

	public function getName(): string
	{
		return match ($this) {
			self::FULL_PREPAYMENT => 'Предоплата 100%',
			self::PREPAYMENT => 'Предоплата',
			self::AVANCE => 'Аванс',
			self::FULL_PAYMENT => 'Полный расчет',
			self::PARTIAL_PAYMENT => 'Частичный расчет',
			self::TO_CREDIT => 'Передача в кредит',
			self::FROM_CREDIT => 'Оплата кредита',
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
