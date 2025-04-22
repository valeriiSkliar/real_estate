<?php

namespace app\enums;

enum PaymentProviders: string
{
    use EnumHelperTrait;

    case YOO_MONEY = 'yoo_money';

    public static function getLabel(string $type): string
    {
        return match ($type) {
            default => 'Оплата картой',
        };
    }
}
