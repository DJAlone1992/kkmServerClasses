<?php

declare(strict_types=1);

namespace Djalone\KkmServerClasses\Tests;

use Djalone\KkmServerClasses\Command;
use PHPUnit\Framework\TestCase;

final class CommandTest extends TestCase
{
    public function test_base_command_validation_success(): void
    {
        $command = new class extends Command {
            protected string $command = 'TestCommand';

            public function toArray(): array
            {
                return ['CustomField' => 'custom'];
            }
        };

        $command
            ->setCashierName('Иванов Иван')
            ->setCashierVatin('123456789012')
            ->setKktNumber('1234567890')
            ->setIdCommand(str_repeat('a', 40))
            ->setTimeout(30)
            ->setNotPrint(true);

        $this->assertTrue($command->isValid());
        $this->assertSame([], $command->getErrors());

        $realArray = $command->toRealArray();
        $this->assertSame('TestCommand', $realArray['Command']);
        $this->assertSame('Иванов Иван', $realArray['CashierName']);
        $this->assertSame('123456789012', $realArray['CashierVatin']);
        $this->assertSame('1234567890', $realArray['KktNumber']);
        $this->assertSame(str_repeat('a', 40), $realArray['IdCommand']);
        $this->assertSame(30, $realArray['Timeout']);
        $this->assertSame(true, $realArray['NotPrint']);
        $this->assertSame('custom', $realArray['CustomField']);

        $decodedJson = json_decode($command->toJson(), true);
        $this->assertSame($realArray, $decodedJson);
    }

    public function test_base_command_validation_returns_all_errors(): void
    {
        $command = new class extends Command {
            protected string $command = 'TestCommand';

            public function toArray(): array
            {
                return [];
            }
        };

        $command
            ->setCashierName('A')
            ->setCashierVatin('123')
            ->setIdCommand('short');

        $this->assertFalse($command->isValid());
        $this->assertSame(
            [
                'Ф.И.О. кассира не может быть короче 3 символов',
                'ИНН кассира должен состоять из 12 цифр',
                'Идентификатор команды не может быть короче 40 символов',
            ],
            $command->getErrors()
        );
    }
}
