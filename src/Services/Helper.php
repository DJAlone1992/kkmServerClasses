<?php

namespace Djalone\KkmServerClasses\Services;
/**
 * Класс для вспомогательных функций.
 */
class Helper
{
	/**
	 * Преобразовать целое значение в строку с плавающей точкой по заданной точности.
	 *
	 * @param int $value      Значение в минимальных единицах.
	 * @param int $precision  Количество десятичных знаков.
	 * @return string
	 */
	public static function toFloat(int $value, int $precision): string
	{
		return number_format($value / 10 ** $precision, $precision, '.', '');
	}
}
