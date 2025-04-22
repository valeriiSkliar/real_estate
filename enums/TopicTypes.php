<?php
namespace app\enums;

enum TopicTypes: int
{
    case OIL = 1;
    case DISEASE = 2;

    /**
     * Возвращает все тарифы в виде массива.
     *
     * @return array
     * @throws \Exception
     */
    public static function getTypes(): array
    {
        $types = [];
        foreach (self::cases() as $case) {
            $types[$case->value] = self::getTypeName($case->value);
        }
        return $types;
    }

    public static function getTypeName(int $type): string
    {
        return match ($type) {
            self::OIL->value => 'Масло',
            self::DISEASE->value => 'Болезнь',
            default => '--Не выбрано--',
        };
    }
}