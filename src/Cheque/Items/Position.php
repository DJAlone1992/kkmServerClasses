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
	 * Сумма позиции.
	 * @var int
	 */
	private readonly int $amount;
	/**
	 * Номер отдела.
	 * @var int
	 */
	private int $department = 0;
	/**
	 * Ставка налога.
	 * @var Tax
	 */
	private Tax $tax = Tax::NDS_NONE;
	/**
	 * Метод расчета.
	 * @var SignMethodCalculation
	 */
	private SignMethodCalculation $signMethodCalculation = SignMethodCalculation::FULL_PAYMENT;
	/**
	 * Объект расчета.
	 * @var SignCalculationObject
	 */
	private SignCalculationObject $signCalculationObject = SignCalculationObject::SERVICE;
	/**
	 * Единица измерения количества.
	 * @var MeasureOfQuantity
	 */
	private MeasureOfQuantity $measureOfQuantity = MeasureOfQuantity::UNITS;
	/**
	 * Тип оплаты.
	 * @var PaymentTypes
	 */
	private PaymentTypes $paymentType = PaymentTypes::Cash;

	/**
	 * Конструктор позиции.
	 *
	 * @param string $name Название позиции.
	 * @param int $price Цена в копейках.
	 * @param int $quantity Количество в тысячных долях (по умолчанию 1000).
	 */
	public function __construct(
		/**
		 * Наименование позиции
		 *
		 * @var string
		 */
		private readonly string $name,
		/**
		 * Цена в копейках
		 * @var int
		 */
		private readonly int $price,
		/**
		 * Количество (в тысячных долях. 1 шт = 1000)
		 * @var int
		 */
		private readonly int $quantity = 1000
	) {
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
	public function setPrice(int|float $price): static
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
	public function setQuantity(int|float $quantity): static
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
	 * @param PaymentTypes $paymentType Тип оплаты.
	 * @return static
	 */
	public function setPaymentType(PaymentTypes $paymentType): static
	{
		$this->paymentType = $paymentType;
		return $this;
	}

	/**
	 * Получить тип оплаты позиции.
	 *
	 * @return PaymentTypes
	 */
	public function getPaymentType(): PaymentTypes
	{
		return $this->paymentType;
	}

	/**
	 * Установить номер отдела.
	 *
	 * @param int $department Номер отдела.
	 * @return static
	 */
	public function setDepartment(int $department): static
	{
		$this->department = $department;
		return $this;
	}
	/**
	 * Установить ставку налога.
	 *
	 * @param Tax $tax Ставка налога.
	 * @return static
	 */
	public function setTax(Tax $tax): static
	{
		$this->tax = $tax;
		return $this;
	}
	/**
	 * Установить метод расчета (полная оплата, аванс и т.п.).
	 *
	 * @param SignMethodCalculation $signMethodCalculation Метод расчета.
	 * @return static
	 */
	public function setSignMethodCalculation(
		SignMethodCalculation $signMethodCalculation
	): static {
		$this->signMethodCalculation = $signMethodCalculation;
		return $this;
	}
	/**
	 * Установить объект расчета (товар, услуга и т.п.).
	 *
	 * @param SignCalculationObject $signCalculationObject Объект расчета.
	 * @return static
	 */
	public function setSignCalculationObject(
		SignCalculationObject $signCalculationObject
	): static {
		$this->signCalculationObject = $signCalculationObject;
		return $this;
	}
	/**
	 * Установить единицу измерения количества.
	 *
	 * @param MeasureOfQuantity $measureOfQuantity Единица измерения количества.
	 * @return static
	 */
	public function setMeasureOfQuantity(
		MeasureOfQuantity $measureOfQuantity
	): static {
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
	 * @return Tax Ставка налога.
	 */
	public function getTax(): Tax
	{
		return $this->tax;
	}
	/**
	 * @return SignMethodCalculation Метод расчета.
	 */
	public function getSignMethodCalculation(): SignMethodCalculation
	{
		return $this->signMethodCalculation;
	}
	/**
	 * @return SignCalculationObject Объект расчета.
	 */
	public function getSignCalculationObject(): SignCalculationObject
	{
		return $this->signCalculationObject;
	}
	/**
	 * @return MeasureOfQuantity Единица измерения количества.
	 */
	public function getMeasureOfQuantity(): MeasureOfQuantity
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
				'Tax' => $this->tax->value,
				'SignMethodCalculation' => $this->signMethodCalculation->value,
				'SignCalculationObject' => $this->signCalculationObject->value,
				'MeasureOfQuantity' => $this->measureOfQuantity->value,
				'internalPaymentType' => $this->paymentType->value,
			],
		];
	}
}
