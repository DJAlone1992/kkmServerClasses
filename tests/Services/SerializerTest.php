<?php

declare(strict_types=1);

namespace Djalone\KkmServerClasses\Tests\Services;

use Djalone\KkmServerClasses\Cheque;
use Djalone\KkmServerClasses\Services\Serializer;
use PHPUnit\Framework\TestCase;

final class SerializerTest extends TestCase
{
	public function test_serialize_and_deserialize_keeps_properties(): void
	{
		$cheque = new Cheque();
		$cheque->setCashierName('A')->setCashierVatin('123456789012');
		$cheque->setKktNumber('1234567890');
		$cheque->setIdCommand(str_repeat('x', 40));
		$cheque->addText(new \Djalone\KkmServerClasses\Cheque\Items\Text('hi'));

		$json = Serializer::serializeCheque($cheque);
		$this->assertIsString($json);

		$restored = Serializer::deserializeCheque($json);
		$this->assertInstanceOf(Cheque::class, $restored);
		$this->assertSame('A', $restored->getCashierName());
		$this->assertSame('hi', $restored->getItems()[0]->getText());
	}
}
