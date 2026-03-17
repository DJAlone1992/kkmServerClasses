<?php

namespace Djalone\KkmServerClasses\Cheque\Enums;

use ReflectionClass;

/**
 * Налоговая ставка
 */
class Tax
{
	public const NDS_0 = 0;
	public const NDS_5 = 5;
	public const NDS_7 = 7;
	public const NDS_10 = 10;
	public const NDS_22 = 22;
	public const NDS_NONE = -1;
	public const NDS_5_105 = 105;
	public const NDS_7_107 = 107;
	public const NDS_22_122 = 122;
	public const NDS_10_110 = 110;

	public static function getName($value): string
	{
		switch ($value) {
			case self::NDS_0:
				return 'НДС 0%';
			case self::NDS_5:
				return 'НДС 5%';
			case self::NDS_7:
				return 'НДС 7%';
			case self::NDS_10:
				return 'НДС 10%';
			case self::NDS_22:
				return 'НДС 22%';
			case self::NDS_NONE:
				return 'Без НДС';
			case self::NDS_5_105:
				return 'НДС 5/105';
			case self::NDS_7_107:
				return 'НДС 7/107';
			case self::NDS_22_122:
				return 'НДС 22/122';
			case self::NDS_10_110:
				return 'НДС 10/110';
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
