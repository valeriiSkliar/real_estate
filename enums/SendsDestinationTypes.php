<?php
namespace app\enums;

enum SendsDestinationTypes: int
{
    use EnumHelperTrait;

    case ALL = 0;
    case ACTIVE = 1;
    case FINISHED = 2;
//    case REFERRAL = 7;
//    case NOT_REFERRAL = 8;

    public static function getLabel(int $type): string
    {
        return match ($type) {
            self::ACTIVE->value => 'Активная подписка',
            self::FINISHED->value => 'Не продлил подписку',
//            self::REFERRAL->value => 'Реферал',
//            self::NOT_REFERRAL->value => 'Не реферал',
            default => 'Все',
        };
    }
}