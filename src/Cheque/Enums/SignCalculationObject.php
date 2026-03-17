<?php

namespace Djalone\KkmServerClasses\Cheque\Enums;

use ReflectionClass;

/**
 *  Признак предмета расчета. Тег ОФД 1212. Для ФФД.1.05 и выше обязательное поле
 */
class SignCalculationObject
{
	// 1: "ТОВАР (наименование и иные сведения, описывающие товар)"
	public const GOODS = 1;
	// 2: "ПОДАКЦИЗНЫЙ ТОВАР (наименование и иные сведения, описывающие товар)"
	public const EXCISABLE_GOODS = 2;
	// 3: "РАБОТА (наименование и иные сведения, описывающие работу)"
	public const WORK = 3;
	// 4: "УСЛУГА (наименование и иные сведения, описывающие услугу)"
	public const SERVICE = 4;
	// 5: "СТАВКА АЗАРТНОЙ ИГРЫ (при осуществлении деятельности по проведению азартных игр)"
	public const GAMBLING_BET = 5;
	// 6: "ВЫИГРЫШ АЗАРТНОЙ ИГРЫ (при осуществлении деятельности по проведению азартных игр)"
	public const GAMBLING_WIN = 6;
	// 7: "ЛОТЕРЕЙНЫЙ БИЛЕТ (при осуществлении деятельности по проведению лотерей)"
	public const LOTTERY_TICKET = 7;
	// 8: "ВЫИГРЫШ ЛОТЕРЕИ (при осуществлении деятельности по проведению лотерей)"
	public const LOTTERY_WIN = 8;
	// 9: "ПРЕДОСТАВЛЕНИЕ РИД (предоставлении прав на использование результатов интеллектуальной деятельности или средств индивидуализации)"
	public const PROVIDING_RID = 9;
	// 10: "ПЛАТЕЖ (аванс, задаток, предоплата, кредит, взнос в счет оплаты, пени, штраф, вознаграждение, бонус и иной аналогичный предмет расчета)"
	public const PAYMENT = 10;
	// 11: "АГЕНТСКОЕ ВОЗНАГРАЖДЕНИЕ (вознаграждение (банковского)платежного агента/субагента, комиссионера, поверенного или иным агентом)"
	public const AGENT_COMMISSION = 11;
	// 12: "СОСТАВНОЙ ПРЕДМЕТ РАСЧЕТА (предмет расчета, состоящем из предметов, каждому из которых может быть присвоено вышестоящее значение"
	public const COMPOSITE = 12;
	// 13: "ИНОЙ ПРЕДМЕТ РАСЧЕТА (предмет расчета, не относящемуся к предметам расчета, которым может быть присвоено вышестоящее значение"
	public const OTHER = 13;
	// 14: "ИМУЩЕСТВЕННОЕ ПРАВО" (передача имущественных прав)
	public const PROPERTY_RIGHT = 14;
	// 15: "ВНЕРЕАЛИЗАЦИОННЫЙ ДОХОД"
	public const UNREALIZED_INCOME = 15;
	// 16: "СТРАХОВЫЕ ВЗНОСЫ" (суммы расходов, уменьшающих сумму налога (авансовых платежей) в соответствии с пунктом 3.1 статьи 346.21 Налогового кодекса Российской Федерации)
	public const INSURANCE_PREMIUM = 16;
	// 17: "ТОРГОВЫЙ СБОР" (суммы уплаченного торгового сбора)
	public const TRADE_FEE = 17;
	// 18: "КУРОРТНЫЙ СБОР"
	public const RESORT_FEE = 18;
	// 19: "ЗАЛОГ"
	public const DEPOSIT = 19;
	// 20: "РАСХОД" - суммы произведенных расходов в соответствии со статьей 346.16 Налогового кодекса Российской Федерации, уменьшающих доход
	public const EXPENSE = 20;
	// 21: "ВЗНОСЫ НА ОБЯЗАТЕЛЬНОЕ ПЕНСИОННОЕ СТРАХОВАНИЕ ИП" или "ВЗНОСЫ НА ОПС ИП"
	public const INSURANCE_PREMIUM_IP = 21;
	// 22: "ВЗНОСЫ НА ОБЯЗАТЕЛЬНОЕ ПЕНСИОННОЕ СТРАХОВАНИЕ" или "ВЗНОСЫ НА ОПС"
	public const INSURANCE_PREMIUM_OSP = 22;
	// 23: "ВЗНОСЫ НА ОБЯЗАТЕЛЬНОЕ МЕДИЦИНСКОЕ СТРАХОВАНИЕ ИП" или "ВЗНОСЫ НА ОМС ИП"
	public const MEDICAL_INSURANCE_IP = 23;
	// 24: "ВЗНОСЫ НА ОБЯЗАТЕЛЬНОЕ МЕДИЦИНСКОЕ СТРАХОВАНИЕ" или "ВЗНОСЫ НА ОМС"
	public const MEDICAL_INSURANCE = 24;
	// 25: "ВЗНОСЫ НА ОБЯЗАТЕЛЬНОЕ СОЦИАЛЬНОЕ СТРАХОВАНИЕ" или "ВЗНОСЫ НА ОСС"
	public const SOCIAL_INSURANCE = 25;
	// 26: "ПЛАТЕЖ КАЗИНО" прием и выплата денежных средств при осуществлении казино и залами игровых автоматов расчетов с использованием обменных знаков игорного заведения
	public const CASINO_PAYMENT = 26;

	public static function getName($value): string
	{
		switch ($value) {
			case self::GOODS:
				return 'Товар';
			case self::EXCISABLE_GOODS:
				return 'Подакцизный товар';
			case self::WORK:
				return 'Работа';
			case self::SERVICE:
				return 'Услуга';
			case self::GAMBLING_BET:
				return 'Ставка азартной игры';
			case self::GAMBLING_WIN:
				return 'Выигрыш азартной игры';
			case self::LOTTERY_TICKET:
				return 'Лотерейный билет';
			case self::LOTTERY_WIN:
				return 'Выигрыш лотереи';
			case self::PROVIDING_RID:
				return 'Предоставление РИД';
			case self::PAYMENT:
				return 'Платеж';
			case self::AGENT_COMMISSION:
				return 'Агентское вознаграждение';
			case self::COMPOSITE:
				return 'Составной предмет расчета';
			case self::OTHER:
				return 'Иной предмет расчета';
			case self::PROPERTY_RIGHT:
				return 'Имущественное право';
			case self::UNREALIZED_INCOME:
				return 'Внереализационный доход';
			case self::INSURANCE_PREMIUM:
				return 'Страховые взносы';
			case self::TRADE_FEE:
				return 'Торговый сбор';
			case self::RESORT_FEE:
				return 'Курортный сбор';
			case self::DEPOSIT:
				return 'Залог';
			case self::EXPENSE:
				return 'Расход';
			case self::INSURANCE_PREMIUM_IP:
				return 'Взносы на ОПС ИП';
			case self::INSURANCE_PREMIUM_OSP:
				return 'Взносы на ОПС';
			case self::MEDICAL_INSURANCE_IP:
				return 'Взносы на ОМС ИП';
			case self::MEDICAL_INSURANCE:
				return 'Взносы на ОМС';
			case self::SOCIAL_INSURANCE:
				return 'Взносы на ОСС';
			case self::CASINO_PAYMENT:
				return 'Платеж казино';
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
