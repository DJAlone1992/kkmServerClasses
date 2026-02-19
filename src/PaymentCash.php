<?php

namespace Djalone\KkmServerClasses;

class PaymentCash extends Command
{

    private readonly int $amount;

    public function __construct(string $cashierName, string $cashierVatin, string $kktNumber, string $idCommand)
    {
        parent::__construct($cashierName, $cashierVatin, $kktNumber, $idCommand);
        $this->command = "PaymentCash";
    }
    public function toArray(): array
    {
        return [
            'Amount'=>Helper::toFloat($this->amount,2)
        ];
    }
}
