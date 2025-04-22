<?php
namespace app\enums;

enum OilTypes: string
{
    case SINGLE = 'single';
    case BLEND = 'blend';
    case SUPPLEMENT = 'supplement';
    case OTHER = 'other';

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

    public static function getTypeName(?string $type): string
    {
        return match ($type) {
            self::SINGLE->value => 'Однокомпонентное',
            self::BLEND->value => 'Смесь',
            self::SUPPLEMENT->value => 'БАД',
            self::OTHER->value => 'Другое',
            default => '--Не выбрано--',
        };
    }
}