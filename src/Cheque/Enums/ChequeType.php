<?php

namespace Djalone\KkmServerClasses\Cheque\Enums;
// Тип чека, Тег 1054;
enum ChequeType: int
{

    // 0 – продажа/приход;
    case INCOME=0;
    // 1 – возврат продажи/прихода;
    case INCOME_RETURN=1;
    // 2 – корректировка продажи/прихода;
    case INCOME_CORRECTION=2;
    // 3 – корректировка возврата продажи/прихода; (>=ФФД 1.1)
    case INCOME_RETURN_CORRECTION=3;
    // 10 – покупка/расход;
    case OUTCOME=10;
    // 11 - возврат покупки/расхода;
    case OUTCOME_RETURN=11;
    // 12 – корректировка покупки/расхода;
    case OUTCOME_CORRECTION=12;
    // 13 – корректировка возврата покупки/расхода; (>=ФФД 1.1)
    case OUTCOME_RETURN_CORRECTION=13;
}
