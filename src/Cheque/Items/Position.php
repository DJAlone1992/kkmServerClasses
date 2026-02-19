<?php

namespace Djalone\KkmServerClasses\Cheque\Items;

use Djalone\KkmServerClasses\Cheque\Enums\MeasureOfQuantity;
use Djalone\KkmServerClasses\Cheque\Enums\SignCalculationObject;
use Djalone\KkmServerClasses\Cheque\Enums\SignMethodCalculation;
use Djalone\KkmServerClasses\Cheque\Enums\Tax;
use Djalone\KkmServerClasses\Helper;

class Position extends Item
{
    private readonly int $amount;
    private int $department = 0;
    private Tax $tax = Tax::NDS_NONE;
    private SignMethodCalculation $signMethodCalculation = SignMethodCalculation::FULL_PAYMENT;
    private SignCalculationObject $signCalculationObject = SignCalculationObject::SERVICE;
    private MeasureOfQuantity $measureOfQuantity = MeasureOfQuantity::UNITS;

    public function __construct(private readonly string $name, private readonly int $price, private readonly int $quantity = 1)
    {
        $this->amount = $this->price * $this->quantity;
    }

    public function setDepartment(int $department): static
    {
        $this->department = $department;
        return $this;
    }
    public function setTax(Tax $tax): static
    {
        $this->tax = $tax;
        return $this;
    }
    public function setSignMethodCalculation(SignMethodCalculation $signMethodCalculation): static
    {
        $this->signMethodCalculation = $signMethodCalculation;
        return $this;
    }
    public function setSignCalculationObject(SignCalculationObject $signCalculationObject): static
    {
        $this->signCalculationObject = $signCalculationObject;
        return $this;
    }
    public function setMeasureOfQuantity(MeasureOfQuantity $measureOfQuantity): static
    {
        $this->measureOfQuantity = $measureOfQuantity;
        return $this;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

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
                'MeasureOfQuantity' => $this->measureOfQuantity->value
            ]
        ];
    }
}
