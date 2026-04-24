<?php

namespace Djalone\KkmServerClasses\Cheque\Enums;

/**
 * Тип штрих-кода
 */
enum BarCodeType: string
{
    case QR = 'QR';
    case CODE128 = 'CODE128';
    case CODE39 = 'CODE39';
    case PDF417 = 'PDF417';
    case EAN13 = 'EAN13';

    public function getName(): string
    {
        return match ($this) {
            self::QR => 'QR',
            self::CODE128 => 'CODE128',
            self::CODE39 => 'CODE39',
            self::PDF417 => 'PDF417',
            self::EAN13 => 'EAN13',
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
