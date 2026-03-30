<?php

declare(strict_types=1);

namespace Djalone\KkmServerClasses\Services;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Processor\PsrLogMessageProcessor;

/**
 * Статический логгер на основе Monolog
 */
class Logger
{
	private static ?MonologLogger $instance = null;

	/**
	 * Получить экземпляр логгера
	 */
	public static function getInstance(): MonologLogger
	{
		if (self::$instance === null) {
			self::initialize();
		}

		return self::$instance;
	}

	/**
	 * Инициализация логгера
	 */
	private static function initialize(): void
	{
		// Создаем папку var/logs если не существует
		if (!defined('KKM_SERVER_LOGS_DIR') && !is_null(constant('KKM_SERVER_LOGS_DIR'))) {
			$logsDir = __DIR__ . '/../../var/logs';
		} else {
			$logsDir = constant('KKM_SERVER_LOGS_DIR');
		}
		if (!is_dir($logsDir)) {
			mkdir($logsDir, 0777, true);
		}

		// Создаем логгер
		$logger = new MonologLogger('kkm-server');

		// Добавляем процессор для форматирования сообщений
		$logger->pushProcessor(new PsrLogMessageProcessor());

		// Общий лог: все уровни, ротация файлов (макс 10 файлов, каждый ~10MB для общего размера ~100MB)
		$generalHandler = new RotatingFileHandler(
			$logsDir . '/kkm-server.log',
			10, // Максимум 10 файлов
			\Monolog\Level::Debug,
			true,
			0664,
			true
		);
		$logger->pushHandler($generalHandler);

		// Лог ошибок: только ошибки и выше
		$errorHandler = new RotatingFileHandler(
			$logsDir . '/kkm-server-error.log',
			10, // Максимум 10 файлов
			\Monolog\Level::Error,
			true,
			0664,
			true
		);
		$logger->pushHandler($errorHandler);

		self::$instance = $logger;
	}

	/**
	 * Запрет на создание экземпляров
	 */
	private function __construct() {}

	/**
	 * Запрет на клонирование
	 */
	private function __clone() {}

	/**
	 * Запрет на десериализацию
	 */
	public function __wakeup() {}
}
