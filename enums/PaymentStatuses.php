<?php
namespace app\enums;

enum PaymentStatuses: int
{
    case NEW = 0;
    case SUCCESS = 2;
    case REJECTED = 3;
    case CANCELLED = 4;
    case SUBSCRIPTION_ACTIVATED = 5;
    case SUBSCRIPTION_CANCELLED = 6;
    case SUBSCRIPTION_RENEWED = 7;

    /**
     * Возвращает все тарифы в виде массива.
     *
     * @return array
     */
    public static function getPaymentStatuses(): array
    {
        $payments = [];
        foreach (self::cases() as $case) {
            $payments[$case->value] = self::getPaymentName($case->value);
        }
        return $payments;
    }

    public static function getPaymentName(int $payment): string
    {
        return match ($payment) {
            self::NEW->value => 'Создан',
            self::SUCCESS->value => 'Оплачен',
            self::REJECTED->value => 'Отклонен',
            self::CANCELLED->value => 'Отменен',
            self::SUBSCRIPTION_ACTIVATED->value => 'Подписка активирована',
            self::SUBSCRIPTION_CANCELLED->value => 'Подписка отменена',
            self::SUBSCRIPTION_RENEWED->value => 'Подписка продлена',
            default => 'Создан',
        };
    }
}