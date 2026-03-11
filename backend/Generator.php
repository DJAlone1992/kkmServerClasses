<?php

/**
 * Генератор команд для ККТ (контрольно-кассовой техники)
 *
 * Этот скрипт обрабатывает AJAX-запросы, валидирует параметры,
 * создает соответствующие объекты команд и возвращает их в JSON-формате.
 */

// Импортируем необходимые классы команд
use Djalone\KkmServerClasses\CloseShift;
use Djalone\KkmServerClasses\DepositingCash;
use Djalone\KkmServerClasses\GetDataKKT;
use Djalone\KkmServerClasses\OpenShift;
use Djalone\KkmServerClasses\PaymentCash;
use Djalone\KkmServerClasses\XReport;
use Djalone\KkmServerClasses\DeviceList;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Djalone\KkmServerClasses\Services\Logger;

// Подключаем автозагрузчик Composer
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Функция валидации входных параметров запроса
 *
 * @param Request $request Объект запроса Symfony
 * @return array Массив с ошибками валидации
 */
function validateRequest(Request $request): array
{
	$errors = [];

	// Получаем параметры из запроса
	$cashierName = $request->query->get('cashierName');
	$cashierVatin = $request->query->get('cashierVatin');
	$command = $request->query->get('command');
	$kktNumber = $request->query->get('kktNumber');
	$idCommand = $request->query->get('idCommand');

	// Проверяем обязательные параметры
	if (empty($cashierName) || !is_string($cashierName)) {
		$errors[] = 'Не указаны Ф.И.О. кассира или некорректный формат';
	}

	if (
		empty($cashierVatin) ||
		!is_string($cashierVatin) ||
		!preg_match('/^\d{10,12}$/', $cashierVatin)
	) {
		$errors[] =
			'Не указан ИНН кассира или некорректный формат (должен быть 10-12 цифр)';
	}

	if (empty($command) || !is_string($command)) {
		$errors[] = 'Не указана команда или некорректный формат';
	}
	if ($command !== 'DeviceList') {
		// Для команды DeviceList остальные параметры не обязательны
		if (empty($kktNumber) || !is_string($kktNumber)) {
			$errors[] = 'Не указан номер ККТ или некорректный формат';
		}
	}

	if (empty($idCommand) || !is_string($idCommand)) {
		$errors[] = 'Не указан идентификатор команды или некорректный формат';
	}

	return $errors;
}

/**
 * Функция создания объекта команды на основе типа
 *
 * @param string $commandType Тип команды
 * @param string $cashierName ФИО кассира
 * @param string $cashierVatin ИНН кассира
 * @param string $kktNumber Номер ККТ
 * @param string $idCommand Идентификатор команды
 * @param Request $request Объект запроса для дополнительных параметров
 * @return object|null Объект команды или null, если команда не найдена
 */
function createCommand(Request $request)
{
	// Получаем параметры после валидации
	$cashierName = $request->query->get('cashierName');
	$cashierVatin = $request->query->get('cashierVatin');
	$commandType = $request->query->get('command');
	$kktNumber = $request->query->get('kktNumber');
	$idCommand = $request->query->get('idCommand');
	switch ($commandType) {
		case 'openShift':
			return new OpenShift(
				$cashierName,
				$cashierVatin,
				$kktNumber,
				$idCommand
			);

		case 'XReport':
			return new XReport(
				$cashierName,
				$cashierVatin,
				$kktNumber,
				$idCommand
			);

		case 'closeShift':
			return new CloseShift(
				$cashierName,
				$cashierVatin,
				$kktNumber,
				$idCommand
			);

		case 'depositCash':
			$amount = $request->query->get('amount', 0);
			if (!is_numeric($amount) || $amount < 0) {
				throw new InvalidArgumentException(
					'Некорректная сумма для внесения наличных'
				);
			}
			$command = new DepositingCash(
				$cashierName,
				$cashierVatin,
				$kktNumber,
				$idCommand
			);
			$command->setAmount((float) $amount);
			return $command;

		case 'paymentCash':
			$amount = $request->query->get('amount', 0);
			if (!is_numeric($amount) || $amount < 0) {
				throw new InvalidArgumentException(
					'Некорректная сумма для выплаты наличных'
				);
			}
			$command = new PaymentCash(
				$cashierName,
				$cashierVatin,
				$kktNumber,
				$idCommand
			);
			$command->setAmount((float) $amount);
			return $command;

		case 'KKTStatus':
			return new GetDataKKT(
				$cashierName,
				$cashierVatin,
				$kktNumber,
				$idCommand
			);

		case 'DeviceList':
			return new DeviceList();

		default:
			return null; // Неизвестная команда
	}
}

// Создаем объект запроса из глобальных переменных
$request = Request::createFromGlobals();

// Логируем входящий запрос
Logger::getInstance()->info('Incoming request', [
	'method' => $request->getMethod(),
	'path' => $request->getPathInfo(),
	'query' => $request->query->all(),
	'isAjax' => $request->isXmlHttpRequest(),
]);

// Проверяем, что запрос является AJAX
if (!$request->isXmlHttpRequest()) {
	$response = new Response(
		'Only ajax requests allowed',
		Response::HTTP_BAD_REQUEST
	);
	$response->prepare($request);
	$response->send();
	exit();
}

try {
	// Валидируем входные параметры
	$errors = validateRequest($request);

	if (!empty($errors)) {
		Logger::getInstance()->warning('Request validation failed', [
			'errors' => $errors,
			'query' => $request->query->all(),
		]);
		$response = new JsonResponse(
			[
				'error' => true,
				'errorCode' => 1,
				'errorText' => implode(', ', $errors),
			],
			Response::HTTP_BAD_REQUEST
		);
		$response->prepare($request);
		$response->send();
		exit();
	}

	// Создаем объект команды
	$command = createCommand($request);

	if ($command === null) {
		Logger::getInstance()->warning('Unknown command requested', [
			'command' => $request->query->get('command'),
			'query' => $request->query->all(),
		]);
		$response = new JsonResponse(
			[
				'error' => true,
				'errorCode' => 2,
				'errorText' => 'Неизвестная команда',
			],
			Response::HTTP_BAD_REQUEST
		);
		$response->prepare($request);
		$response->send();
		exit();
	}

	// Логируем успешное создание команды
	Logger::getInstance()->info('Command created successfully', [
		'commandType' => get_class($command),
		'idCommand' => $request->query->get('idCommand'),
	]);

	// Возвращаем успешный ответ с данными команды
	$response = new JsonResponse(
		['error' => false, 'command' => $command->toRealArray()],
		Response::HTTP_OK
	);
	$response->prepare($request);
	$response->send();
} catch (InvalidArgumentException $e) {
	// Обработка ошибок валидации параметров команд
	Logger::getInstance()->error('Invalid argument exception', [
		'message' => $e->getMessage(),
		'query' => $request->query->all(),
	]);
	$response = new JsonResponse(
		[
			'error' => true,
			'errorCode' => 3,
			'errorText' => $e->getMessage(),
		],
		Response::HTTP_BAD_REQUEST
	);
	$response->prepare($request);
	$response->send();
} catch (Exception $e) {
	// Обработка общих исключений
	Logger::getInstance()->error('Unexpected exception', [
		'message' => $e->getMessage(),
		'trace' => $e->getTraceAsString(),
		'query' => $request->query->all(),
	]);
	$response = new JsonResponse(
		[
			'error' => true,
			'errorCode' => 4,
			'errorText' => 'Внутренняя ошибка сервера',
		],
		Response::HTTP_INTERNAL_SERVER_ERROR
	);
	$response->prepare($request);
	$response->send();
}
