<?php

namespace Djalone\KkmServerClasses\Cheque\Enums;

/**
 * Тип чека, Тег 1054;
 */
class ChequeType
{
    // 0 – продажа/приход;
    public const INCOME = 0;
    // 1 – возврат продажи/прихода;
    public const INCOME_RETURN = 1;
    // 2 – корректировка продажи/прихода;
    public const INCOME_CORRECTION = 2;
    // 3 – корректировка возврата продажи/прихода; (>=ФФД 1.1)
    public const INCOME_RETURN_CORRECTION = 3;
    // 10 – покупка/расход;
    public const OUTCOME = 10;
    // 11 - возврат покупки/расхода;
    public const OUTCOME_RETURN = 11;
    // 12 – корректировка покупки/расхода;
    public const OUTCOME_CORRECTION = 12;
    // 13 – корректировка возврата покупки/расхода; (>=ФФД 1.1)
    public const OUTCOME_RETURN_CORRECTION = 13;
}
