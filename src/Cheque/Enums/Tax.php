<?php

namespace Djalone\KkmServerClasses\Cheque\Enums;

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

	public function getName(): string
	{
		return match ($this) {
			self::NDS_0 => 'НДС 0%',
			self::NDS_5 => 'НДС 5%',
			self::NDS_7 => 'НДС 7%',
			self::NDS_10 => 'НДС 10%',
			self::NDS_22 => 'НДС 22%',
			self::NDS_NONE => 'Без НДС',
			self::NDS_5_105 => 'НДС 5/105',
			self::NDS_7_107 => 'НДС 7/107',
			self::NDS_22_122 => 'НДС 22/122',
			self::NDS_10_110 => 'НДС 10/110',
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
