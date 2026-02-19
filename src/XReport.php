<?php

namespace Djalone\KkmServerClasses;

class XReport extends Command
{
    public function __construct(string $cashierName, string $cashierVatin, string $kktNumber, string $idCommand)
    {
        parent::__construct($cashierName, $cashierVatin, $kktNumber, $idCommand);
        $this->command = "XReport";
    }
    public function toArray(): array
    {
        return [];
    }
}
