<?php
namespace app\enums;

enum TradeTypes: string
{
    use EnumHelperTrait;

    case SALE = 'sale';
    case RENT = 'rent';

    public static function getLabel(string $type): string
    {
        return match ($type) {
            self::SALE->value => 'Продажа',
            self::RENT->value => 'Аренда',
            default => '-',
        };
    }
}