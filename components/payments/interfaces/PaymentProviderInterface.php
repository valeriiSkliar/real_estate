<?php

namespace app\components\payments\interfaces;

use app\models\BotUsers;
use app\models\Payments;
use app\models\Tariffs;

interface PaymentProviderInterface
{
    public function generatePaymentLink(BotUsers $user, Tariffs $tariff, Payments $payment): string;
}