<?php

namespace Djalone\KkmServerClasses;
use Djalone\KkmServerClasses\Command;

/**
 * Команда получения списка устройств
 */
class DeviceList extends Command
{
	public function __construct()
	{
		parent::__construct('', '', '', '');
		$this->command = 'List';
	}

	public function toArray(): array
	{
		return [
			'Active' => true,
			'OnOff' => true,
		];
	}

	public function isValid(): bool
	{
		return true;
	}
}
