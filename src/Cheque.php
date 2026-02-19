<?php

namespace Djalone\KkmServerClasses;

use Djalone\KkmServerClasses\Cheque\Enums\ChequeType;
use Djalone\KkmServerClasses\Cheque\Items\Item;
use Djalone\KkmServerClasses\Cheque\Items\Position;

class Cheque extends Command
{
    /**
     * @var array<Item> $items
     */
    private array $items = [];
    private int $cash = 0;
    private int $electronicPayment = 0;
    private int $advancePayment = 0;
    private int $credit = 0;
    private int $cashProvision = 0;
    private ChequeType $chequeType = ChequeType::INCOME;
    private bool $isFiscal = false;
    private string $clientAddress = '';
    private string $clientInfo = '';
    private string $clientINN = '';
    private string $RRNCode = '';
    private string $AuthorizationCode = '';

    public function __construct(string $cashierName, string $cashierVatin, string $kktNumber, string $idCommand)
    {
        parent::__construct($cashierName, $cashierVatin, $kktNumber, $idCommand);
        $this->command = "RegisterCheck";
    }

    public function addItem(Item $item): static
    {
        $this->items[] = $item;
        if ($item instanceof Position) {
            $this->cash += $item->getAmount();
        }
        return $this;
    }
    public function setCash(int $cash): static
    {
        $this->cash = $cash;
        return $this;
    }
    public function setElectronicPayment(int $electronicPayment): static
    {
        $this->electronicPayment = $electronicPayment;
        return $this;
    }
    public function setAdvancePayment(int $advancePayment): static
    {
        $this->advancePayment = $advancePayment;
        return $this;
    }
    public function setCredit(int $credit): static
    {
        $this->credit = $credit;
        return $this;
    }
    public function setCashProvision(int $cashProvision): static
    {
        $this->cashProvision = $cashProvision;
        return $this;
    }
    public function setChequeType(ChequeType $chequeType): static
    {
        $this->chequeType = $chequeType;
        return $this;
    }
    public function setIsFiscal(bool $isFiscal): static
    {
        $this->isFiscal = $isFiscal;
        return $this;
    }
    public function setClientAddress(string $clientAddress): static
    {
        $this->clientAddress = $clientAddress;
        return $this;
    }
    public function setClientInfo(string $clientInfo): static
    {
        $this->clientInfo = $clientInfo;
        return $this;
    }
    public function setClientINN(string $clientINN): static
    {
        $this->clientINN = $clientINN;
        return $this;
    }
    public function setRRNCode(string $RRNCode): static
    {
        $this->RRNCode = $RRNCode;
        return $this;
    }
    public function setAuthorizationCode(string $AuthorizationCode): static
    {
        $this->AuthorizationCode = $AuthorizationCode;
        return $this;
    }
    public function toArray(): array
    {

        return [
            'ClientAddress' => $this->clientAddress,
            'ClientInfo' => $this->clientInfo,
            'ClientINN' => $this->clientINN,
            'RRNCode' => $this->RRNCode,
            'AuthorizationCode' => $this->AuthorizationCode,
            'IsFiscalCheck' => $this->isFiscal,
            'TypeCheck' => $this->chequeType->value,
            'CheckStrings' => array_map(fn(Item $item) => $item->toArray(), $this->items),
            'Cash' => Helper::toFloat($this->cash, 5),
            'ElectronicPayment' => Helper::toFloat($this->electronicPayment, 5),
            'AdvancePayment' => Helper::toFloat($this->advancePayment, 5),
            'Credit' => Helper::toFloat($this->credit, 5),
            'CashProvision' => Helper::toFloat($this->cashProvision, 5),
            'NumberCopies' => 0,
            'InternetMode' => false,
            'PrintSlipAfterCheck' => false,
            'PrintSlipForCashier' => true
        ];
    }
}
