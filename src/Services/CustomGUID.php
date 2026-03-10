<?php

namespace Djalone\KkmServerClasses\Services;

use Ramsey\Uuid\Uuid;
/**
 * Класс для генерации GUID
 */
class CustomGUID
{
	/**
	 * Генерирует GUID в формате 40 символов, состоящий из UUIDv7 и UUIDv4, разделенных дефисом.
	 *
	 * @return string<40>
	 */
	public static function getCommandGuid(): string
	{
		$uuid = Uuid::uuid7()->toString();
		$uuidSalt = Uuid::uuid4()->toString();
		$offset = 40 - strlen($uuid) - 1;
		$uuidString = substr($uuidSalt, -$offset) . '-' . $uuid;
		if (strlen($uuidString) !== 40) {
			throw new \Exception('Generated GUID has invalid length');
		}
		return $uuidString;
	}
}
