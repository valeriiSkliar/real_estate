<?php
namespace app\enums;

enum PropertyTypes: string
{
    use EnumHelperTrait;

    case APP = 'app';
    case HOUSE = 'house';
    case LAND = 'land';

    public static function getLabel(string $type): string
    {
        return match ($type) {
            self::APP->value => 'Квартира',
            self::HOUSE->value => 'Дом',
            self::LAND->value => 'Участок',
            default => '-',
        };
    }
}