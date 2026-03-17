<?php

declare(strict_types=1);

namespace Djalone\KkmServerClasses\Tests\Cheque\Items;

use Djalone\KkmServerClasses\Cheque\Enums\MeasureOfQuantity;
use Djalone\KkmServerClasses\Cheque\Enums\PaymentTypes;
use Djalone\KkmServerClasses\Cheque\Enums\SignCalculationObject;
use Djalone\KkmServerClasses\Cheque\Enums\SignMethodCalculation;
use Djalone\KkmServerClasses\Cheque\Enums\Tax;
use Djalone\KkmServerClasses\Cheque\Items\Position;
use PHPUnit\Framework\TestCase;

final class PositionTest extends TestCase
{
    public function test_constructor_sets_readonly_properties(): void
    {
        $position = new Position('Product Name', 10000, 2000);

        $this->assertSame('Product Name', $position->getName());
        $this->assertSame(10000, $position->getPrice());
        $this->assertSame(2000, $position->getQuantity());
    }

    public function test_constructor_calculates_amount_correctly(): void
    {
        $position = new Position('Product', 10000, 3000);

        $this->assertSame(30000000, $position->getAmount());
    }

    public function test_constructor_with_default_quantity(): void
    {
        $position = new Position('Product', 5000);

        $this->assertSame(1000, $position->getQuantity());
        $this->assertSame(5000000, $position->getAmount());
    }

    public function test_default_department_is_zero(): void
    {
        $position = new Position('Product', 1000);

        $this->assertSame(0, $position->getDepartment());
    }

    public function test_default_tax_is_nds_none(): void
    {
        $position = new Position('Product', 1000);

        $this->assertSame(Tax::NDS_NONE, $position->getTax());
    }

    public function test_default_sign_method_calculation_is_full_payment(): void
    {
        $position = new Position('Product', 1000);

        $this->assertSame(SignMethodCalculation::FULL_PAYMENT, $position->getSignMethodCalculation());
    }

    public function test_default_sign_calculation_object_is_service(): void
    {
        $position = new Position('Product', 1000);

        $this->assertSame(SignCalculationObject::SERVICE, $position->getSignCalculationObject());
    }

    public function test_default_measure_of_quantity_is_units(): void
    {
        $position = new Position('Product', 1000);

        $this->assertSame(MeasureOfQuantity::UNITS, $position->getMeasureOfQuantity());
    }

    public function test_default_payment_type_is_cash(): void
    {
        $position = new Position('Product', 1000);

        $this->assertSame(PaymentTypes::Cash, $position->getPaymentType());
    }

    public function test_set_department(): void
    {
        $position = new Position('Product', 1000);
        $result = $position->setDepartment(5);

        $this->assertSame(5, $position->getDepartment());
        $this->assertSame($position, $result);
    }

    public function test_set_tax(): void
    {
        $position = new Position('Product', 1000);
        $result = $position->setTax(Tax::NDS_10);

        $this->assertSame(Tax::NDS_10, $position->getTax());
        $this->assertSame($position, $result);
    }

    public function test_set_sign_method_calculation(): void
    {
        $position = new Position('Product', 1000);
        $result = $position->setSignMethodCalculation(SignMethodCalculation::AVANCE);

        $this->assertSame(SignMethodCalculation::AVANCE, $position->getSignMethodCalculation());
        $this->assertSame($position, $result);
    }

    public function test_set_sign_calculation_object(): void
    {
        $position = new Position('Product', 1000);
        $result = $position->setSignCalculationObject(SignCalculationObject::GOODS);

        $this->assertSame(SignCalculationObject::GOODS, $position->getSignCalculationObject());
        $this->assertSame($position, $result);
    }

    public function test_set_measure_of_quantity(): void
    {
        $position = new Position('Product', 1000);
        $result = $position->setMeasureOfQuantity(MeasureOfQuantity::KILOGRAMS);

        $this->assertSame(MeasureOfQuantity::KILOGRAMS, $position->getMeasureOfQuantity());
        $this->assertSame($position, $result);
    }

    public function test_set_payment_type(): void
    {
        $position = new Position('Product', 1000);
        $result = $position->setPaymentType(PaymentTypes::Electronic);

        $this->assertSame(PaymentTypes::Electronic, $position->getPaymentType());
        $this->assertSame($position, $result);
    }

    public function test_fluent_interface_method_chaining(): void
    {
        $position = (new Position('Product', 1000))
            ->setDepartment(3)
            ->setTax(Tax::NDS_10)
            ->setSignMethodCalculation(SignMethodCalculation::FULL_PAYMENT)
            ->setSignCalculationObject(SignCalculationObject::GOODS)
            ->setMeasureOfQuantity(MeasureOfQuantity::LITER)
            ->setPaymentType(PaymentTypes::Electronic);

        $this->assertSame(3, $position->getDepartment());
        $this->assertSame(Tax::NDS_10, $position->getTax());
        $this->assertSame(SignMethodCalculation::FULL_PAYMENT, $position->getSignMethodCalculation());
        $this->assertSame(SignCalculationObject::GOODS, $position->getSignCalculationObject());
        $this->assertSame(MeasureOfQuantity::LITER, $position->getMeasureOfQuantity());
        $this->assertSame(PaymentTypes::Electronic, $position->getPaymentType());
    }

    public function test_to_array_structure(): void
    {
        $position = new Position('Test Product', 10000, 2000);
        $position->setDepartment(5);
        $position->setTax(Tax::NDS_10);
        $position->setPaymentType(PaymentTypes::Electronic);

        $array = $position->toArray();

        $this->assertArrayHasKey('Register', $array);
        $this->assertIsArray($array['Register']);
        $this->assertArrayHasKey('Name', $array['Register']);
        $this->assertArrayHasKey('Quantity', $array['Register']);
        $this->assertArrayHasKey('Price', $array['Register']);
        $this->assertArrayHasKey('Amount', $array['Register']);
        $this->assertArrayHasKey('Department', $array['Register']);
        $this->assertArrayHasKey('Tax', $array['Register']);
        $this->assertArrayHasKey('SignMethodCalculation', $array['Register']);
        $this->assertArrayHasKey('SignCalculationObject', $array['Register']);
        $this->assertArrayHasKey('MeasureOfQuantity', $array['Register']);
        $this->assertArrayHasKey('internalPaymentType', $array['Register']);
    }

    public function test_to_array_values(): void
    {
        $position = new Position('Test Product', 10000, 2000);
        $position->setDepartment(5);
        $position->setTax(Tax::NDS_10);

        $array = $position->toArray();
        $register = $array['Register'];

        $this->assertSame('Test Product', $register['Name']);
        $this->assertSame('2.000', $register['Quantity']);
        $this->assertSame('100.00', $register['Price']);
        $this->assertSame('200.00000', $register['Amount']);
        $this->assertSame(5, $register['Department']);
        $this->assertSame(Tax::NDS_10, $register['Tax']);
        $this->assertSame(SignMethodCalculation::FULL_PAYMENT, $register['SignMethodCalculation']);
        $this->assertSame(SignCalculationObject::SERVICE, $register['SignCalculationObject']);
        $this->assertSame(MeasureOfQuantity::UNITS, $register['MeasureOfQuantity']);
        $this->assertSame(PaymentTypes::Cash, $register['internalPaymentType']);
    }
}
