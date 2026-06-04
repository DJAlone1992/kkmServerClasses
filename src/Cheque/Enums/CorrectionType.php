<?php

namespace Djalone\KkmServerClasses\Cheque\Enums;

/**
 * Тип корректировки Тег 1173
 */
enum CorrectionType: int
{
    // 0 – самостоятельно;
    case INDIVIDUALLY = 0;
        // 1 – по предписанию
    case PRESCRIPTION = 1;

    public function getName(): string
    {
        return match ($this) {
            self::INDIVIDUALLY => 'Самостоятельно',
            self::PRESCRIPTION => 'По предписанию',
            default => 'Не известно'
        };
    }
    public static function getArray(): array
    {
        $result = [];
        foreach (self::cases() as $value) {
            $result[$value->value] = $value->getName();
        }
        return $result;
    }
}
