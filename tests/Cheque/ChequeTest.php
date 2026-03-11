<?php

declare(strict_types=1);

namespace Djalone\KkmServerClasses\Tests\Cheque;

use Djalone\KkmServerClasses\Cheque;
use Djalone\KkmServerClasses\Cheque\Items\Position;
use Djalone\KkmServerClasses\Cheque\Items\Text;
use Djalone\KkmServerClasses\Cheque\Enums\Tax;
use Djalone\KkmServerClasses\Cheque\Enums\PaymentTypes;
use PHPUnit\Framework\TestCase;

final class ChequeTest extends TestCase
{
	public function test_add_text_and_position_and_totals(): void
	{
		$cheque = new Cheque();
		$cheque->addText(new Text('foo'));
		$position = new Position('item', 1000, 2000);
		$position->setPaymentType(PaymentTypes::Cash);
		$cheque->addPosition($position);

		$this->assertCount(2, $cheque->getItems());
		$this->assertSame(1000 * 2000, $position->getAmount()); // sanity
		$array = $cheque->toArray();
		$this->assertArrayHasKey('CheckStrings', $array);
	}

	public function test_basic_properties_and_validation(): void
	{
		$cheque = new Cheque();
		$cheque
			->setCashierName('Ivan')
			->setCashierVatin('123456789012')
			->setKktNumber('1234567890')
			->setIdCommand(str_repeat('a', 40))
			->setClientAddress('test@domain.com')
			->setClientInfo('info');

		$this->assertFalse(
			$cheque->isValid(),
			'should be invalid without items'
		);
		$cheque->addText(new Text('x'));
		$this->assertTrue($cheque->isValid());
	}

	public function test_setters_and_getters_for_misc(): void
	{
		$cheque = new Cheque();
		$cheque
			->setCash(100)
			->setElectronicPayment(200)
			->setAdvancePayment(300)
			->setCredit(400)
			->setCashProvision(500);
		$this->assertSame(100, $cheque->getCash());
		$this->assertSame(200, $cheque->getElectronicPayment());
		$this->assertSame(300, $cheque->getAdvancePayment());
		$this->assertSame(400, $cheque->getCredit());
		$this->assertSame(500, $cheque->getCashProvision());
	}
}
