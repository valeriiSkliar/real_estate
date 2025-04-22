<?php
namespace app\enums;

enum DiseaseTypes: string
{
    case INFECTIOUS = 'infectious';
    case NOT_INFECTIOUS = 'not-infectious';

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

    public static function getTypeName(string $type): string
    {
        return match ($type) {
            self::INFECTIOUS->value => 'Инфекционное',
            self::NOT_INFECTIOUS->value => 'Не инфекционное',
            default => '--Не выбрано--',
        };
    }
}