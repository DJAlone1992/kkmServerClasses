<?php

namespace Djalone\KkmServerClasses\Services;

use Djalone\KkmServerClasses\Cheque;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;
/**
 * Класс для сериализации и десериализации объектов чека.
 */
class Serializer
{
	/**
	 * @var array<string, bool|string|string[]>
	 */
	protected static array $serializerContext = [
		AbstractNormalizer::IGNORED_ATTRIBUTES => [
			'serializer',
			'serializerContext',
			'errors',
			'valid',
			'items',
		],
		AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
		AbstractObjectNormalizer::SKIP_UNINITIALIZED_VALUES => true,
		DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s',
	];
	/**
	 * Сериализовать объект чека в JSON.
	 *
	 * @param Cheque $cheque
	 * @return string
	 */
	public static function serializeCheque(Cheque $cheque): string
	{
		$serializer = self::getSerializer();

		return $serializer->serialize(
			$cheque,
			JsonEncoder::FORMAT,
			self::$serializerContext
		);
	}

	/**
	 * Десериализовать JSON обратно в объект Cheque.
	 *
	 * @param string $chequeJson
	 * @return Cheque
	 */
	public static function deserializeCheque(string $chequeJson): Cheque
	{
		$context = self::$serializerContext;
		$context[AbstractNormalizer::OBJECT_TO_POPULATE] = Cheque::class;
		$serializer = self::getSerializer();
		return $serializer->deserialize(
			$chequeJson,
			Cheque::class,
			JsonEncoder::FORMAT,
			$context
		);
	}

	/**
	 * Построить и вернуть экземпляр Symfony Serializer
	 * с преднастроенными нормализаторами и кодировщиками.
	 *
	 * @return SymfonySerializer
	 */
	private static function getSerializer(): SymfonySerializer
	{
		$encoders = [new JsonEncoder()];
		$normalizers = [
			new DateTimeNormalizer(),
			new BackedEnumNormalizer(),
			new ArrayDenormalizer(),
			new ObjectNormalizer(
				propertyTypeExtractor: new PropertyInfoExtractor(
					typeExtractors: [
						new PhpDocExtractor(),
						new ReflectionExtractor(),
					]
				)
			),
		];
		return new SymfonySerializer(
			$normalizers,
			$encoders,
			self::$serializerContext
		);
	}
}
