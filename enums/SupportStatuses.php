<?php

namespace app\enums;

enum  SupportStatuses: int
{
    use EnumHelperTrait;

    case NEW = 0;
    case PROCESSED = 1;

    public static function getLabel(int $type): string
    {
        return match ($type) {
            self::PROCESSED->value => 'Новое',
            default => 'Обработанное',
        };
    }
}
