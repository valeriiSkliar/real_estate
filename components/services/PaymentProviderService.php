<?php

namespace app\components\services;

use app\components\payments\providers\PaymentProviderFactory;
use app\enums\PaymentStatuses;
use app\helpers\ErrorLogHelper;
use app\models\BotUsers;
use app\models\Payments;
use app\models\Promocodes;
use app\models\Tariffs;
use Exception;
use Yii;

class PaymentProviderService
{
    /**
     * @throws Exception
     */
    public function getPaymentUrl(BotUsers $user, Tariffs $tariff, ?string $promoCode = null): string
    {
        $payment = $this->getPayment($user, $tariff, $promoCode);

        return PaymentProviderFactory::getProvider($tariff->provider)->generatePaymentLink($user, $tariff, $payment);
    }

    /**
     * @throws Exception
     */
    public function getPayment(BotUsers $user, Tariffs $tariff, ?string $promoCode = null): Payments
    {
        $payment = new Payments();
        $payment->user_id = $user->id;
        $payment->amount = $tariff->price;
        $payment->tariff_id = $tariff->id;
        $payment->status = PaymentStatuses::NEW->value;
        $payment->created_at = date('Y-m-d H:i:s');

        $promoModel = Promocodes::findOne(['code' => $promoCode]);

        if ($promoModel && $promoModel->isValid()) {
            $payment->promo_code = $promoModel->code;
        }

        if (!$payment->save()) {
            Yii::$app->response->statusCode = 400;
            ErrorLogHelper::logPaymentInfo($payment->getErrors(), 'Ошибка сохранения платежа в БД');

            throw new Exception('Error while saving payment');
        }

        return $payment;
    }
}