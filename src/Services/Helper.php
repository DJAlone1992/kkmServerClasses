<?php

namespace Djalone\KkmServerClasses\Services;

use Djalone\KkmServerClasses\Cheque;

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
	/**
	 * Преобразовать строку с плавающей точкой в целое значение с заданной точностью.
	 *
	 * @param float $value     Значение в виде строки с плавающей точкой.
	 * @param int $precision  Количество десятичных знаков.
	 * @return int
	 */
	public static function toInt(float $value, int $precision): int
	{
		return round($value, $precision, PHP_ROUND_HALF_DOWN) *
			10 ** $precision;
	}
	/**
	 * Генерирует HTML-форму для отправки данных чека на печать и автоматически отправляет её при загрузке страницы.
	 * @param Cheque $cheque Объект чека, который нужно распечатать.
	 * @param string $callbackUrl URL для обратного вызова после печати.
	 * @return string HTML-код формы и скрипта для автоматической отправки.
	 */
	public static function echoForm(Cheque $cheque, string $callbackUrl, string $customID = '', bool $autoSubmit = true, string $frontendDir = '/frontend'): string
	{
		$chequeJSON = htmlspecialchars(Serializer::serializeCheque($cheque));
		$callbackUrl = htmlspecialchars($callbackUrl);
		$path = $frontendDir . '/printer.php';
		$result = [
			"<form target=\"_blank\" id=\"printerRequestForm_{$customID}\" action=\"{$path}\" method=\"post\">",
			"<input type=\"hidden\" name=\"callbackUrl\" value=\"{$callbackUrl}\">",
			"<input type=\"hidden\" name=\"chequeJson\" value=\"{$chequeJSON}\">",
			'</form>',
			'<script>',
			"document.addEventListener('DOMContentLoaded', function() {",
			$autoSubmit ? " document.getElementById('printerRequestForm_{$customID}').submit();" : '',
			'});',
			'</script>',
		];
		return implode($result);
	}

	public static function formSubmitScript(string $customID = ''): string
	{
		return "document.getElementById('printerRequestForm_{$customID}').submit();";
	}
}
