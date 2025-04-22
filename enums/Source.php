<?php
namespace app\enums;

enum Source: string
{
    use EnumHelperTrait;

    case WHATSAPP = 'whatsapp';
    case TELEGRAM = 'telegram';
    case REALTOR = 'realtor';

    public static function getLabel(string $type): string
    {
        return match ($type) {
            self::WHATSAPP->value => 'Whatsapp',
            self::TELEGRAM->value => 'Telegram',
            self::REALTOR->value => 'Риелтор',
            default => '-',
        };
    }

    public static function getPlatformCases(): array
    {
        return [
            self::WHATSAPP->value => self::getLabel(self::WHATSAPP->value),
            self::TELEGRAM->value => self::getLabel(self::TELEGRAM->value),
        ];
    }
}