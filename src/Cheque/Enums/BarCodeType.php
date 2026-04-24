<?php

namespace Djalone\KkmServerClasses\Cheque\Enums;

use ReflectionClass;

class BarCodeType
{
    public const QR = 'QR';
    public const CODE128 = 'CODE128';
    public const CODE39 = 'CODE39';
    public const PDF417 = 'PDF417';
    public const EAN13 = 'EAN13';
    public function getName(): string
    {
        switch ($this) {
            case self::QR:
                return 'QR';
            case self::CODE128:
                return 'CODE128';
            case self::CODE39:
                return 'CODE39';
            case self::PDF417:
                return 'PDF417';
            case self::EAN13:
                return 'EAN13';
            default:
                return 'Не известно';
        }
    }
    public static function tryFrom(?string $value): ?string
    {
        if (is_null($value)) {
            return null;
        }
        switch (strtoupper($value)) {
            case self::QR:
                return self::QR;
            case self::CODE128:
                return self::CODE128;
            case self::CODE39:
                return self::CODE39;
            case self::PDF417:
                return self::PDF417;
            case self::EAN13:
                return self::EAN13;
            default:
                return null;
        }
    }
    public static function getArray(): array
    {
        $reflection = new ReflectionClass(self::class);
        $cases = $reflection->getConstants();
        $result = [];
        foreach ($cases as $value) {
            $result[$value] = self::getName($value);
        }
        return $result;
    }
}
