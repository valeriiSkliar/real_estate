<?php
namespace app\enums;

enum Tariff: int
{
    case NONE = 0;
    case MONTH_1 = 1;
    case MONTH_12 = 12;

    /**
     * Возвращает все тарифы в виде массива.
     *
     * @return array
     */
    public static function getTariffs(): array
    {
        $tariffs = [];
        foreach (self::cases() as $case) {
            if ($case->value === self::NONE->value) {
                continue;
            }

            $tariffs[$case->value] = self::getTariffName($case->value);
        }
        return $tariffs;
    }

    public static function getTariffName(int $tariff): string
    {
        return match ($tariff) {
            self::MONTH_1->value => '1 месяц',
            self::MONTH_12->value => '12 месяцев',
            default => 'Без тарифа',
        };
    }

    public static function getTariffSlug(int $tariff): string
    {
        return match ($tariff) {
            self::MONTH_1->value => '1_month',
            self::MONTH_12->value => '12_month',
            default => 'no tariff',
        };
    }
}