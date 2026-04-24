<?php

namespace Djalone\KkmServerClasses;

class GetDataCheck extends Command
{
    private int $fiscalNumber = 0;
    private int $numberOfCopies = 0;

    public function __construct(
        string $cashierName = '',
        string $cashierVatin = '',
        string $kktNumber = '',
        string $idCommand = ''
    ) {
        parent::__construct(
            $cashierName,
            $cashierVatin,
            $kktNumber,
            $idCommand
        );
        $this->command = 'GetDataCheck';
    }

    public function setFiscalNumber(int $fiscalNumber): static
    {
        $this->fiscalNumber = $fiscalNumber;
        return $this;
    }

    public function setNumberOfCopies(int $numberOfCopies): static
    {
        $this->numberOfCopies = $numberOfCopies;
        return $this;
    }

    public function getFiscalNumber(): int
    {
        return $this->fiscalNumber;
    }

    public function getNumberOfCopies(): int
    {
        return $this->numberOfCopies;
    }


    /**
     * @return array<string, int>
     */
    public function toArray(): array
    {
        return [
            'FiscalNumber' => $this->fiscalNumber,
            'NumberCopies' => $this->numberOfCopies
        ];
    }
}
