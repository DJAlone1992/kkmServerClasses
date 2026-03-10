<?php

namespace Djalone\KkmServerClasses;
/**
 * Команда получения данных ККТ.
 */
class GetDataKKT extends Command
{
	/**
	 * Конструктор команды получения данных ККТ.
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
		$this->command = 'GetDataKKT';
	}
	/**
	 * Возвращает пустой массив параметров для команды получения данных ККТ.
	 *
	 * @return array{}
	 */
	public function toArray(): array
	{
		return [];
	}
}
