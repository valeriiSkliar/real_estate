<?php
namespace app\enums;

enum SendsProviderTypes: int
{
    use EnumHelperTrait;

    case TELEGRAM = 1;
    case ALL = 0;
    case WEB = 2;
    case MOBILE = 3;

    public static function getLabel(int $type): string
    {
        return match ($type) {
            self::TELEGRAM->value => 'Телеграм',
            self::WEB->value => 'Веб',
            self::MOBILE->value => 'Мобильные',
            default => 'Все',
        };
    }
}