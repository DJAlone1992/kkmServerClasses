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
	 * @param string $customID Префикс ID формы
	 * @param array $fields Массив дополнительных полей формата [<inputName> => <value> ]
	 * @param bool $autoSubmit Автоматически отправлять форму после загрузки страницы.
	 * @param string $frontendDir Путь к директории с фронтендом.
	 * @return string HTML-код формы и скрипта для автоматической отправки.
	 */
	public static function echoForm(Cheque $cheque, string $callbackUrl, string $customID = '', array $fields = [], bool $autoSubmit = true, string $frontendDir = '/frontend'): string
	{
		$chequeJSON = htmlspecialchars(Serializer::serializeCheque($cheque));
		$callbackUrl = htmlspecialchars($callbackUrl);
		$path = $frontendDir . '/printer.php';
		$result = [
			"<form target=\"_blank\" id=\"printerRequestForm_{$customID}\" action=\"{$path}\" method=\"post\">",
		];
		$fields['callbackUrl'] = $callbackUrl;
		$fields['chequeJson'] = $chequeJSON;
		foreach ($fields as $name => $value) {
			$result[] = "<input type=\"hidden\" name=\"{$name}\" value=\"{$value}\">";
		}
		$result[] = '</form>';
		if ($autoSubmit) {
			$result[] = '<script>';
			$result[] = "document.addEventListener('DOMContentLoaded', function() {";
			$result[] = " document.getElementById('printerRequestForm_{$customID}').submit();";
			$result[] = '});';
			$result[] = '</script>';
		}
		return implode('', $result);
	}

	/**
	 * Генерирует HTML script tag для добавления HTML-формы отправки данных чека на печать и автоматически отправляет её при загрузке страницы.
	 * @param Cheque $cheque Объект чека, который нужно распечатать.
	 * @param string $callbackUrl URL для обратного вызова после печати.
	 * @param string $customID Префикс ID формы
	 * @param array $fields Массив дополнительных полей формата [<inputName> => <value> ]
	 * @param bool $autoSubmit Автоматически отправлять форму после загрузки страницы.
	 * @param string $frontendDir Путь к директории с фронтендом.
	 * @return string HTML-код формы и скрипта для автоматической отправки.
	 */
	public static function jsEchoForm(
		Cheque $cheque,
		string $callbackUrl,
		string $customID = '',
		array $fields = [],
		bool $autoSubmit = true,
		string $frontendDir = '/frontend'
	): string {
		$chequeJSON = htmlspecialchars(Serializer::serializeCheque($cheque));
		$callbackUrl = htmlspecialchars($callbackUrl);
		$path = $frontendDir . '/printer.php';

		$fields['callbackUrl'] = $callbackUrl;
		$fields['chequeJson'] = $chequeJSON;

		$result = [
			'<script>',
			"document.addEventListener('DOMContentLoaded', function() {",
			"	let body = document.getElementsByTagName('body')[0];",
			"	let form = document.createElement('form');",
			"	form.target = '_blank';",
			"	form.id = 'printerRequestForm_{$customID}';",
			"	form.action = '{$path}';",
			"	form.method = 'POST';",
		];
		foreach ($fields as $name => $value) {
			$result[] = '';
			$result[] = "	let input_{$name} = document.createElement('input');";
			$result[] = "	input_{$name}.type = 'hidden';";
			$result[] = "	input_{$name}.name = '{$name}';";
			$result[] = "	input_{$name}.value = '{$value}';";
			$result[] = "	form.appendChild(input_{$name});";
		}

		$result[] =	'';
		$result[] = '	body.appendChild(form);';
		$result[] = $autoSubmit ? 'form.submit();' : '';
		$result[] = '});';
		$result[] = '</script>';

		return implode("\n", $result);
	}
	/**
	 * Генерирует JavaScript код для отправки формы печати чека.
	 *
	 * @param string $customID Идентификатор формы, если используется несколько форм на странице.
	 * @return string JavaScript код для отправки формы.
	 */

	public static function formSubmitScript(string $customID = ''): string
	{
		return "document.getElementById('printerRequestForm_{$customID}').submit();";
	}
}
