<?php

namespace Djalone\KkmServerClasses;

abstract class Command
{
    protected string $command;
    protected int $timeout = 60;
    protected bool $notPrint = false;

    abstract public function toArray(): array;
    public function __construct(protected string $cashierName, protected string $cashierVatin, protected string $kktNumber, protected string $idCommand)
    {
    }


    public function toJson(): string
    {
        $childArray = $this->toArray();
        $myArray = [
            'Command' => $this->command,
            'KktNumber' => $this->kktNumber,
            'Timeout' => $this->timeout,
            'IdCommand' => $this->idCommand,
            'CashierName' => $this->cashierName,
            'CashierVatin' => $this->cashierVatin,
            'NotPrint' => $this->notPrint
        ];
        return json_encode(array_merge($myArray, $childArray),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
    }
}
