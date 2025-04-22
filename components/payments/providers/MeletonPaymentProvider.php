<?php

namespace app\components\payments\providers;

use app\components\payments\interfaces\PaymentProviderInterface;
use app\helpers\ErrorLogHelper;
use app\models\BotUsers;
use app\models\Payments;
use app\models\Tariffs;
use Exception;

class MeletonPaymentProvider implements PaymentProviderInterface
{

    /**
     * @throws Exception
     */
    public function generatePaymentLink(BotUsers $user, Tariffs $tariff, Payments $payment): string
    {
        try {
            return $tariff->link . '?' . http_build_query([
                    'hidden_0' => $user->id,
                    'name' => $user->fio
                ]);
        } catch (Exception $e) {
            ErrorLogHelper::logPaymentInfo($e->getMessage(), 'Meleton payment error');

            throw $e;
        }
    }
}