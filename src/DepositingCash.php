<?php

namespace Djalone\KkmServerClasses;

use Djalone\KkmServerClasses\Services\Helper;
/**
 * Команда внесения наличных.
 */
class DepositingCash extends Command
{
	private int $amount = 0;

	/**
	 * Конструктор команды внесения наличных.
	 *
	 * @param string $cashierName Имя кассира.
	 * @param string $cashierVatin ИНН кассира.
	 * @param string $kktNumber Номер ККТ.
	 * @param string $idCommand Идентификатор команды.
	 */
	public function __construct(
		string $cashierName = '',
		string $cashierVatin = '',
		string $kktNumber = '',
		string $idCommand = ''
	) {
		parent::__construct(
			$cashierName,
			$cashierVatin,
			$kktNumber,
			$idCommand
		);
		$this->command = 'DepositingCash';
	}
	/**
	 * Установить сумму пополнения.
	 *
	 * Если передано целое число, то считается, что передано количество копеек.
	 * Если передано число с плавающей точкой, то считается, что передано количество рублей и количество будет перечислено в копейках.
	 *
	 * @param int|float $amount
	 * @return static
	 */
	public function setAmount(int|float $amount)
	{
		if (is_float($amount)) {
			$amount = Helper::toInt($amount, 2);
		}
		$this->amount = $amount;
		return $this;
	}
	public function getAmount(): int
	{
		return $this->amount;
	}
	/**
	 * @return array<string, string>
	 */
	public function toArray(): array
	{
		return [
			'Amount' => Helper::toFloat($this->amount, 2),
		];
	}
}
