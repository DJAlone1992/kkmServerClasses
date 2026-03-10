<?php

namespace Djalone\KkmServerClasses;
/**
 * Команда закрытия смены.
 */
class CloseShift extends Command
{
	/**
	 * Конструктор команды закрытия смены.
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
		$this->command = 'CloseShift';
	}
	/**
	 * Возвращает пустой массив параметров для закрытия смены.
	 *
	 * @return array{}
	 */
	public function toArray(): array
	{
		return [];
	}
}
