<?php

namespace Djalone\KkmServerClasses;

/**
 * Абстрактный класс команды.
 */
abstract class Command
{
	/**
	 * @var string
	 */
	protected string $CashierName = '';
	/**
	 * @var string
	 */
	protected string $CashierVatin = '';
	/**
	 * @var string
	 */
	protected string $KktNumber = '';
	/**
	 * @var string
	 */
	protected string $IdCommand = '';
	/**
	 * @var string[]
	 */
	protected array $errors = [];
	/**
	 * Название команды.
	 * @var string $command
	 */
	protected string $command;
	/**
	 * Таймаут в секундах.
	 * @var int $timeout
	 */
	protected int $timeout = 60;
	/**
	 * Флаг не печатать.
	 * @var bool $notPrint
	 */
	protected bool $notPrint = false;
	/**
	 * Возвращает массив параметров команды.
	 */
	abstract public function toArray(): array;

	/**
	 * Конструктор базовой команды.
	 *
	 * @param string $CashierName  Имя кассира (необязательно).
	 * @param string $CashierVatin ИНН/ВАТ кассира (необязательно).
	 * @param string $KktNumber    Номер ККТ (необязательно).
	 * @param string $IdCommand    Уникальный идентификатор команды (необязательно).
	 */
	public function __construct(string $CashierName = '', string $CashierVatin = '', string $KktNumber = '', string $IdCommand = '')
	{
		/**
		 * @var string $CashierName
		 */
		$this->CashierName = $CashierName;
		/**
		 * @var string $CashierVatin
		 */
		$this->CashierVatin = $CashierVatin;
		/**
		 * @var string $KktNumber
		 */
		$this->KktNumber = $KktNumber;
		/**
		 * @var string $IdCommand
		 */
		$this->IdCommand = $IdCommand;
	}

	/**
	 * Установить имя кассира.
	 *
	 * @param string $CashierName
	 * @return static
	 */
	public function setCashierName(string $CashierName)
	{
		$this->CashierName = $CashierName;
		return $this;
	}
	/**
	 * Установить ИНН кассира.
	 *
	 * @param string $CashierVatin
	 * @return static
	 */
	public function setCashierVatin(string $CashierVatin)
	{
		$this->CashierVatin = $CashierVatin;
		return $this;
	}
	/**
	 * Установить номер ККТ.
	 *
	 * @param string $KktNumber
	 * @return static
	 */
	public function setKktNumber(string $KktNumber)
	{
		$this->KktNumber = $KktNumber;
		return $this;
	}
	/**
	 * Установить идентификатор команды.
	 *
	 * @param string $IdCommand
	 * @return static
	 */
	public function setIdCommand(string $IdCommand)
	{
		$this->IdCommand = $IdCommand;
		return $this;
	}
	/**
	 * Установить таймаут в секундах.
	 *
	 * @param int $timeout
	 * @return static
	 */
	public function setTimeout(int $timeout)
	{
		$this->timeout = $timeout;
		return $this;
	}
	/**
	 * Переключить флаг не печатать.
	 *
	 * @param bool $notPrint
	 * @return static
	 */
	public function setNotPrint(bool $notPrint)
	{
		$this->notPrint = $notPrint;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCashierName(): string
	{
		return $this->CashierName;
	}
	/**
	 * @return string
	 */
	public function getCashierVatin(): string
	{
		return $this->CashierVatin;
	}
	/**
	 * @return string
	 */
	public function getKktNumber(): string
	{
		return $this->KktNumber;
	}
	/**
	 * @return string
	 */
	public function getIdCommand(): string
	{
		return $this->IdCommand;
	}
	/**
	 * @return int
	 */
	public function getTimeout(): int
	{
		return $this->timeout;
	}
	/**
	 * @return bool
	 */
	public function getNotPrint(): bool
	{
		return $this->notPrint;
	}
	/**
	 * Convert command object to associative array including base fields.
	 *
	 * @return array<string,mixed>
	 */
	public function toRealArray(): array
	{
		$childArray = $this->toArray();
		$myArray = [
			'Command' => $this->command,
			'KktNumber' => $this->KktNumber,
			'Timeout' => $this->timeout,
			'IdCommand' => $this->IdCommand,
			'CashierName' => $this->CashierName,
			'CashierVatin' => $this->CashierVatin,
			'NotPrint' => $this->notPrint,
		];
		return array_merge($myArray, $childArray);
	}
	/**
	 * Преобразует объект команды в JSON.
	 *
	 * @return string
	 */
	public function toJson(): string
	{
		$childArray = $this->toArray();
		$myArray = [
			'Command' => $this->command,
			'KktNumber' => $this->KktNumber,
			'Timeout' => $this->timeout,
			'IdCommand' => $this->IdCommand,
			'CashierName' => $this->CashierName,
			'CashierVatin' => $this->CashierVatin,
			'NotPrint' => $this->notPrint,
		];
		return json_encode(
			array_merge($myArray, $childArray),
			JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
		);
	}
	/**
	 * Проверка валидности команды.
	 *
	 * @return bool True если команда валидна, иначе false.
	 */
	public function isValid(): bool
	{
		$error = false;
		if (strlen($this->CashierName) < 3) {
			$error = true;
			$this->errors[] = 'Ф.И.О. кассира не может быть короче 3 символов';
		}
		if (strlen($this->CashierVatin) != 12) {
			$error = true;
			$this->errors[] = 'ИНН кассира должен состоять из 12 цифр';
		}
		/*	if (strlen($this->KktNumber) < 10) {
			$error = true;
			$this->errors[] = 'Номер ККТ не может быть короче 10 символов';
		}*/
		if (strlen($this->IdCommand) != 40) {
			$error = true;
			$this->errors[] =
				'Идентификатор команды не может быть короче 40 символов';
		}

		return !$error;
	}
	/**
	 * @return array<string>
	 */
	public function getErrors(): array
	{
		return $this->errors;
	}
}
