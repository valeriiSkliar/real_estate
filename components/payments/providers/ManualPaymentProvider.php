<?php

namespace app\components\payments\providers;

use app\components\payments\interfaces\PaymentProviderInterface;
use app\helpers\ErrorLogHelper;
use app\models\BotUsers;
use app\models\Payments;
use app\models\Tariffs;

class ManualPaymentProvider implements PaymentProviderInterface
{
    public function generatePaymentLink(BotUsers $user, Tariffs $tariff, Payments $payment): string
    {
        try {
           return $tariff->link;
        } catch (\Exception $e) {
            ErrorLogHelper::logPaymentInfo('Request error: ' . $e->getMessage(), 'Ручная оплата ошибка при возврате платежной ссылки для тарифа '  . $tariff->id);

            throw new \RuntimeException('Error while generating payment link');
        }
    }
}