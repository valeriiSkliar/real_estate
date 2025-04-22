<?php

namespace app\enums;

trait EnumHelperTrait
{
    /**
     * Возвращает все кейсы в виде массива.
     *
     * @return array
     */
    public static function getAllCases(): array
    {
        try {
            $array = [];
            foreach (self::cases() as $case) {
                $array[$case->value] = self::getLabel($case->value);
            }
            return $array;
        } catch (\Exception $e) {
            return [];
        }

    }
}