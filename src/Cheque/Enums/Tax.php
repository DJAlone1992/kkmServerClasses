<?php

namespace Djalone\KkmServerClasses\Cheque\Enums;
/**
 * Налоговая ставка
 */
enum Tax: int
{
	case NDS_0 = 0;
	case NDS_5 = 5;
	case NDS_7 = 7;
	case NDS_10 = 10;
	case NDS_22 = 22;
	case NDS_NONE = -1;
	case NDS_5_105 = 105;
	case NDS_7_107 = 107;
	case NDS_22_122 = 122;
	case NDS_10_110 = 110;
}
