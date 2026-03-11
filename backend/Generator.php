<?php

/**
 * Генератор команд для ККТ (контрольно-кассовой техники)
 *
 * Этот скрипт обрабатывает AJAX-запросы, валидирует параметры,
 * создает соответствующие объекты команд и возвращает их в JSON-формате.
 */

// Включаем отображение ошибок только в режиме разработки
// В продакшене ошибки должны логироваться, а не отображаться
ini_set('display_errors', 1);

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

// Импортируем классы Monolog для логирования
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\UidProcessor;

// Подключаем автозагрузчик Composer
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Функция инициализации логгера
 *
 * @return Logger Объект логгера Monolog
 */
function initializeLogger(): Logger
{
	// Создаем логгер с именем 'generator'
	$logger = new Logger('generator');

	// Добавляем уникальный идентификатор для каждого запроса
	$logger->pushProcessor(new UidProcessor());

	// Создаем директорию для логов, если она не существует
	$logDirectory = __DIR__ . '/../logs';
	if (!is_dir($logDirectory)) {
		mkdir($logDirectory, 0755, true);
	}

	// Добавляем обработчик для записи в файл
	$logFile = $logDirectory . '/generator.log';
	$fileHandler = new StreamHandler($logFile, Logger::DEBUG);
	$logger->pushHandler($fileHandler);

	// Добавляем обработчик для записи ошибок в отдельный файл
	$errorLogFile = $logDirectory . '/generator_errors.log';
	$errorHandler = new StreamHandler($errorLogFile, Logger::WARNING);
	$logger->pushHandler($errorHandler);

	return $logger;
}

/**
 * Обработчик PHP ошибок
 *
 * Перехватывает все PHP ошибки и логирует их
 *
 * @param int $errno Код ошибки
 * @param string $errstr Сообщение об ошибке
 * @param string $errfile Файл, где произошла ошибка
 * @param int $errline Строка, где произошла ошибка
 * @param array $errcontext Контекст ошибки
 * @return bool Возвращает false для продолжения стандартной обработки
 */
function errorHandler(
	int $errno,
	string $errstr,
	string $errfile,
	int $errline,
	array $errcontext = []
): bool {
	global $logger;

	// Определяем уровень логирования на основе типа ошибки
	$logLevel = match ($errno) {
		E_ERROR,
		E_CORE_ERROR,
		E_COMPILE_ERROR,
		E_USER_ERROR
			=> Logger::CRITICAL,
		E_WARNING,
		E_CORE_WARNING,
		E_COMPILE_WARNING,
		E_USER_WARNING
			=> Logger::WARNING,
		E_NOTICE,
		E_USER_NOTICE,
		E_STRICT,
		E_DEPRECATED,
		E_USER_DEPRECATED
			=> Logger::NOTICE,
		default => Logger::ERROR,
	};

	// Получаем backtrace для дополнительной информации
	$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

	// Логируем ошибку
	$logger->log($logLevel, 'PHP ошибка', [
		'error_code' => $errno,
		'error_message' => $errstr,
		'file' => $errfile,
		'line' => $errline,
		'context' => $errcontext,
		'backtrace' => $backtrace,
		'error_type' => match ($errno) {
			E_ERROR => 'E_ERROR',
			E_WARNING => 'E_WARNING',
			E_PARSE => 'E_PARSE',
			E_NOTICE => 'E_NOTICE',
			E_CORE_ERROR => 'E_CORE_ERROR',
			E_CORE_WARNING => 'E_CORE_WARNING',
			E_COMPILE_ERROR => 'E_COMPILE_ERROR',
			E_COMPILE_WARNING => 'E_COMPILE_WARNING',
			E_USER_ERROR => 'E_USER_ERROR',
			E_USER_WARNING => 'E_USER_WARNING',
			E_USER_NOTICE => 'E_USER_NOTICE',
			E_STRICT => 'E_STRICT',
			E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
			E_DEPRECATED => 'E_DEPRECATED',
			E_USER_DEPRECATED => 'E_USER_DEPRECATED',
			default => 'UNKNOWN_ERROR',
		},
	]);

	// Возвращаем false, чтобы позволить стандартному обработчику PHP продолжить работу
	return false;
}

/**
 * Обработчик непойманных исключений
 *
 * Перехватывает все непойманные исключения и логирует их
 *
 * @param Throwable $exception Исключение
 */
function exceptionHandler(Throwable $exception): void
{
	global $logger;

	// Логируем критическую ошибку
	$logger->critical('Непойманное исключение', [
		'exception_class' => get_class($exception),
		'message' => $exception->getMessage(),
		'file' => $exception->getFile(),
		'line' => $exception->getLine(),
		'trace' => $exception->getTraceAsString(),
		'code' => $exception->getCode(),
	]);

	// Отправляем JSON ответ клиенту
	$response = new JsonResponse(
		[
			'error' => true,
			'errorCode' => 5,
			'errorText' => 'Критическая ошибка сервера',
		],
		Response::HTTP_INTERNAL_SERVER_ERROR
	);

	// Проверяем, был ли уже отправлен заголовок
	if (!headers_sent()) {
		$request = Request::createFromGlobals();
		$response->prepare($request);
		$response->send();
	}

	exit(1);
}

/**
 * Обработчик завершения скрипта
 *
 * Перехватывает фатальные ошибки при завершении скрипта
 */
function shutdownHandler(): void
{
	global $logger;

	// Получаем информацию о последней ошибке
	$error = error_get_last();

	if ($error !== null) {
		// Определяем, является ли ошибка фатальной
		$fatalErrors = [
			E_ERROR,
			E_PARSE,
			E_CORE_ERROR,
			E_CORE_WARNING,
			E_COMPILE_ERROR,
			E_COMPILE_WARNING,
		];

		if (in_array($error['type'], $fatalErrors)) {
			// Логируем фатальную ошибку
			$logger->critical('Фатальная ошибка при завершении скрипта', [
				'error_type' => $error['type'],
				'message' => $error['message'],
				'file' => $error['file'],
				'line' => $error['line'],
				'error_type_name' => match ($error['type']) {
					E_ERROR => 'E_ERROR',
					E_PARSE => 'E_PARSE',
					E_CORE_ERROR => 'E_CORE_ERROR',
					E_CORE_WARNING => 'E_CORE_WARNING',
					E_COMPILE_ERROR => 'E_COMPILE_ERROR',
					E_COMPILE_WARNING => 'E_COMPILE_WARNING',
					default => 'UNKNOWN_FATAL_ERROR',
				},
			]);
		}
	}
}

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

	if (empty($kktNumber) || !is_string($kktNumber)) {
		$errors[] = 'Не указан номер ККТ или некорректный формат';
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
function createCommand(
	string $commandType,
	string $cashierName,
	string $cashierVatin,
	string $kktNumber,
	string $idCommand,
	Request $request
) {
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

	// Получаем параметры после валидации
	$cashierName = $request->query->get('cashierName');
	$cashierVatin = $request->query->get('cashierVatin');
	$commandType = $request->query->get('command');
	$kktNumber = $request->query->get('kktNumber');
	$idCommand = $request->query->get('idCommand');

	// Создаем объект команды
	$command = createCommand(
		$commandType,
		$cashierName,
		$cashierVatin,
		$kktNumber,
		$idCommand,
		$request
	);

	if ($command === null) {
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

	// Возвращаем успешный ответ с данными команды
	$response = new JsonResponse(
		['error' => false, 'command' => $command->toRealArray()],
		Response::HTTP_OK
	);
	$response->prepare($request);
	$response->send();
} catch (InvalidArgumentException $e) {
	// Обработка ошибок валидации параметров команд
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
