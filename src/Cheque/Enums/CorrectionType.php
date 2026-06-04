<?php

namespace Djalone\KkmServerClasses\Cheque\Enums;

use ReflectionClass;

class CorrectionType
{
    public const INDIVIDUALLY = 0;
    public const PRESCRIPTION = 1;
    public function getName(): string
    {
        switch ($this) {
            case self::INDIVIDUALLY:
                return 'Самостоятельно';
            case self::PRESCRIPTION:
                return 'По предписанию';
            default:
                return 'Не известно';
        }
    }
    public static function getArray(): array
    {
        $reflection = new ReflectionClass(self::class);
        $cases = $reflection->getConstants();
        $result = [];
        foreach ($cases as $value) {
            $result[$value] = self::getName();
        }
        return $result;
    }
}
