<?php

namespace app\enums;

enum  ActiveStatuses: int
{
    use EnumHelperTrait;

    case HIDDEN = 0;
    case ACTIVE = 1;

    public static function getLabel(int $type): string
    {
        return match ($type) {
            self::ACTIVE->value => 'Активный',
            default => 'Скрытый',
        };
    }
}
