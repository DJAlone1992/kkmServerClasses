<?php

namespace Djalone\KkmServerClasses;

use DateTime;
use Djalone\KkmServerClasses\Cheque\Enums\ChequeType;
use Djalone\KkmServerClasses\Cheque\Enums\CorrectionType;
use InvalidArgumentException;
use Override;

class CorrectionCheque extends Cheque
{
    /**
     * Тип корректировки Тег 1173
     * @var CorrectionType
     */
    private CorrectionType $correctionType = CorrectionType::INDIVIDUALLY;
    /**
     * Дата документа основания для коррекции, Тег ОФД 1178
     * @var DateTime
     */
    private DateTime $correctionBaseDate;
    /**
     * Номер документа основания для коррекции, Тег ОФД 1179
     * @var string
     */
    private string $correctionBaseNumber;
    /**
     * Наименование документа основания для коррекции, Тег ОФД 1177
     * @var string
     */
    private string $correctionBaseName;

    public function __construct(string $cashierName = '', string $cashierVatin = '', string $kktNumber = '', string $idCommand = '')
    {
        parent::__construct($cashierName, $cashierVatin, $kktNumber, $idCommand);
        $this->setIsFiscal(true);
    }
    public function setCorrectionType(CorrectionType $correctionType): static
    {
        $this->correctionType = $correctionType;
        return $this;
    }

    public function getCorrectionType(): CorrectionType
    {
        return $this->correctionType;
    }

    public function setCorrectionBaseDate(DateTime $correctionBaseDate): static
    {
        $this->correctionBaseDate = $correctionBaseDate;
        return $this;
    }
    public function getCorrectionBaseDate(): DateTime
    {
        return $this->correctionBaseDate;
    }
    public function setCorrectionBaseNumber(string $correctionBaseNumber): static
    {
        $this->correctionBaseNumber = $correctionBaseNumber;
        return $this;
    }
    public function getCorrectionBaseNumber(): string
    {
        return $this->correctionBaseNumber;
    }
    public function setCorrectionBaseName(string $correctionBaseName): static
    {
        $this->correctionBaseName = $correctionBaseName;
        return $this;
    }
    public function getCorrectionBaseName(): string
    {
        return $this->correctionBaseName;
    }
    #[Override]
    protected function isValidType(): bool
    {
        return $this->getChequeType()->getForCorrection();
    }
    /**
     * Установить тип чека.
     *
     * @param ChequeType $chequeType
     * @throws InvalidArgumentException
     * @return static Текущий объект для цепочки.
     */
    #[Override]
    public function setChequeType(ChequeType $chequeType): static
    {
        if (!$chequeType->getForCorrection()) {
            throw new InvalidArgumentException("Чек коррекции не может иметь тип чека продажи/возврата");
        }
        $this->chequeType = $chequeType;
        return $this;
    }
    #[Override]
    public function toArray(): array
    {
        $array = parent::toArray();
        $array['CorrectionType'] = $this->correctionType->value;
        $array['CorrectionBaseDate'] = $this->correctionBaseDate->format('Y-m-dTH:i:s');
        $array['CorrectionBaseNumber'] = $this->correctionBaseNumber;
        $array['CorrectionBaseName'] = $this->correctionBaseName;
        return $array;
    }

    /**
     * Создание экземпляра чека коррекции
     *
     * @return static
     */
    public static function getNewCorrectionCheque(
        string $cashierName = '',
        string $cashierVatin = '',
        string $kktNumber = '',
        string $idCommand = ''
    ): static {
        return new static($cashierName, $cashierVatin, $kktNumber, $idCommand);
    }
}
