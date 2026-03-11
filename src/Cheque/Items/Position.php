<?php

namespace Djalone\KkmServerClasses\Cheque\Items;

use Djalone\KkmServerClasses\Cheque\Enums\MeasureOfQuantity;
use Djalone\KkmServerClasses\Cheque\Enums\PaymentTypes;
use Djalone\KkmServerClasses\Cheque\Enums\SignCalculationObject;
use Djalone\KkmServerClasses\Cheque\Enums\SignMethodCalculation;
use Djalone\KkmServerClasses\Cheque\Enums\Tax;
use Djalone\KkmServerClasses\Services\Helper;

/**
 * Класс позиции чека.
 */
class Position extends Item
{
	/**
	 * @var string
	 * @readonly
	 */
	private string $name;
	/**
	 * @var int
	 * @readonly
	 */
	private int $price;
	/**
	 * @var int
	 * @readonly
	 */
	private int $quantity = 1000;
	/**
	 * Сумма позиции.
	 * @var int
	 * @readonly
	 */
	private int $amount;
	/**
	 * Номер отдела.
	 * @var int
	 */
	private int $department = 0;
	/**
	 * Ставка налога.
	 * @var int
	 */
	private int $tax = Tax::NDS_NONE;
	/**
	 * Метод расчета.
	 * @var int
	 */
	private int $signMethodCalculation = SignMethodCalculation::FULL_PAYMENT;
	/**
	 * Объект расчета.
	 * @var int
	 */
	private int $signCalculationObject = SignCalculationObject::SERVICE;
	/**
	 * Единица измерения количества.
	 * @var int
	 */
	private int $measureOfQuantity = MeasureOfQuantity::UNITS;
	/**
	 * Тип оплаты.
	 * @var int
	 */
	private int $paymentType = PaymentTypes::Cash;

	/**
	 * Конструктор позиции.
	 *
	 * @param string $name Название позиции.
	 * @param int $price Цена в копейках.
	 * @param int $quantity Количество в тысячных долях (по умолчанию 1000).
	 */
	public function __construct(
		string $name,
		int $price,
		int $quantity = 1000
	) {
		/**
		 * Наименование позиции
		 *
		 * @var string
		 */
		$this->name = $name;
		/**
		 * Цена в копейках
		 * @var int
		 */
		$this->price = $price;
		/**
		 * Количество (в тысячных долях. 1 шт = 1000)
		 * @var int
		 */
		$this->quantity = $quantity;
		$this->amount = $this->price * $this->quantity;
	}
	/**
	 * Установить цену позиции.
	 *
	 * Если передано целое число, то считается, что передано количество копеек.
	 * Если передано число с плавающей точкой, то считается, что передано количество рублей и количество будет перечислено в копейках.
	 * @param int|float $price
	 *
	 * @return static
	 */
	public function setPrice($price)
	{
		if (is_float($price)) {
			$price = Helper::toInt($price,2);
		}
		$this->price = $price;
		return $this;
	}

	/**
	 * Установить количество позиции.
	 * Если передано целое число, то считается, что передано количество в тысячных долях (1 шт = 1000).
	 * Если передано число с плавающей точкой, то считается, что передано количество в штуках и количество будет перечислено в тысячных долях.
	 * @param int|float $quantity
	 *
	 * @return static
	 */
	public function setQuantity($quantity)
	{
		if (is_float($quantity)) {
			$quantity = Helper::toInt($quantity,3);
		}
		$this->quantity = $quantity;
		return $this;
	}
	/**
	 * Установить тип оплаты для позиции.
	 *
	 * @param mixed $paymentType Тип оплаты.
	 * @return static
	 * @param \Djalone\KkmServerClasses\Cheque\Enums\PaymentTypes::* $paymentType
	 */
	public function setPaymentType($paymentType)
	{
		$this->paymentType = $paymentType;
		return $this;
	}

	/**
	 * Получить тип оплаты позиции.
	 *
	 * @return int
	 */
	public function getPaymentType(): int
	{
		return $this->paymentType;
	}

	/**
	 * Установить номер отдела.
	 *
	 * @param int $department Номер отдела.
	 * @return static
	 */
	public function setDepartment(int $department)
	{
		$this->department = $department;
		return $this;
	}
	/**
	 * Установить ставку налога.
	 *
	 * @param mixed $tax Ставка налога.
	 * @return static
	 * @param \Djalone\KkmServerClasses\Cheque\Enums\Tax::* $tax
	 */
	public function setTax($tax)
	{
		$this->tax = $tax;
		return $this;
	}
	/**
	 * Установить метод расчета (полная оплата, аванс и т.п.).
	 *
	 * @param mixed $signMethodCalculation Метод расчета.
	 * @return static
	 * @param \Djalone\KkmServerClasses\Cheque\Enums\SignMethodCalculation::* $signMethodCalculation
	 */
	public function setSignMethodCalculation(
		$signMethodCalculation
	) {
		$this->signMethodCalculation = $signMethodCalculation;
		return $this;
	}
	/**
	 * Установить объект расчета (товар, услуга и т.п.).
	 *
	 * @param mixed $signCalculationObject Объект расчета.
	 * @return static
	 * @param \Djalone\KkmServerClasses\Cheque\Enums\SignCalculationObject::* $signCalculationObject
	 */
	public function setSignCalculationObject(
		$signCalculationObject
	) {
		$this->signCalculationObject = $signCalculationObject;
		return $this;
	}
	/**
	 * Установить единицу измерения количества.
	 *
	 * @param mixed $measureOfQuantity Единица измерения количества.
	 * @return static
	 * @param \Djalone\KkmServerClasses\Cheque\Enums\MeasureOfQuantity::* $measureOfQuantity
	 */
	public function setMeasureOfQuantity(
		$measureOfQuantity
	) {
		$this->measureOfQuantity = $measureOfQuantity;
		return $this;
	}

	/**
	 * Получить рассчитанную сумму позиции (price * quantity).
	 *
	 * @return int
	 */
	public function getAmount(): int
	{
		return $this->amount;
	}

	/**
	 * @return int Номер отдела.
	 */
	public function getDepartment(): int
	{
		return $this->department;
	}
	/**
	 * @return int Ставка налога.
	 */
	public function getTax(): int
	{
		return $this->tax;
	}
	/**
	 * @return int Метод расчета.
	 */
	public function getSignMethodCalculation(): int
	{
		return $this->signMethodCalculation;
	}
	/**
	 * @return int Объект расчета.
	 */
	public function getSignCalculationObject(): int
	{
		return $this->signCalculationObject;
	}
	/**
	 * @return int Единица измерения количества.
	 */
	public function getMeasureOfQuantity(): int
	{
		return $this->measureOfQuantity;
	}
	/**
	 * @return string Наименование позиции.
	 */
	public function getName(): string
	{
		return $this->name;
	}
	/**
	 * @return int Цена позиции.
	 */
	public function getPrice(): int
	{
		return $this->price;
	}
	/**
	 * @return int Количество позиции.
	 */
	public function getQuantity(): int
	{
		return $this->quantity;
	}
	/**
	 * Преобразовать позицию в ассоциативный массив для регистрации.
	 *
	 * @return array<string, array<string, string|int>>
	 */
	public function toArray(): array
	{
		return [
			'Register' => [
				'Name' => $this->name,
				'Quantity' => Helper::toFloat($this->quantity, 3),
				'Price' => Helper::toFloat($this->price, 2),
				'Amount' => Helper::toFloat($this->amount, 5),
				'Department' => $this->department,
				'Tax' => $this->tax,
				'SignMethodCalculation' => $this->signMethodCalculation,
				'SignCalculationObject' => $this->signCalculationObject,
				'MeasureOfQuantity' => $this->measureOfQuantity,
				'internalPaymentType' => $this->paymentType,
			],
		];
	}
}
