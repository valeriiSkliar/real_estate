<?php

namespace app\components\payments\invoices;

use app\enums\PaymentStatuses;
use app\helpers\ErrorLogHelper;
use app\models\BotUsers;
use app\models\Payments;
use app\models\Tariffs;

class MeletonInvoice
{
    public function process(): string
    {
        $headers = \Yii::$app->request->headers;

        $secret = getenv('MELETON_SECRET');


        if (!isset($headers['webhook-key']) || $headers['webhook-key']!= $secret) {
            ErrorLogHelper::logPaymentInfo('wrong secret'. $headers['webhook-key'] ?? '', 'Ошибка оплаты Meleton');

            return 'wrong secret';
        }

        $data = json_decode(\Yii::$app->request->getRawBody(), true, 512, JSON_THROW_ON_ERROR);

        $userId = $data['additional_fields']['hidden_0']['value'] ?? 0;

        if (!$userId){
            ErrorLogHelper::logPaymentInfo('no user_id'. $userId, 'Ошибка оплаты Meleton');

            return 'no user_id';
        }

        $user = BotUsers::findOne($userId);

        if (!$user){
            ErrorLogHelper::logPaymentInfo('no user'. $userId, 'Ошибка оплаты Meleton');

            return 'no user';
        }

        if (!isset($data['product_id']) || !$data['product_id']){
            ErrorLogHelper::logPaymentInfo('no product_id'. $userId, 'Ошибка оплаты Meleton');

            return 'no product_id';
        }

        $tariff = Tariffs::find()
            ->where(['like','link', $data['product_id']])
            ->andFilterWhere(['provider' => 'meleton'])
            ->limit(1)
            ->one();

        if (!$tariff){
            ErrorLogHelper::logPaymentInfo('no tariff'. $userId, 'Ошибка оплаты Meleton');

            return 'no tariff';
        }

        $amount = $tariff->price;

        if($model = Payments::findOne([
            'user_id' => $userId,
            'tariff_id' => $tariff->id,
            'status' => PaymentStatuses::NEW->value,
        ])){
            $model->status = PaymentStatuses::SUCCESS->value;

            if($model->save()) {
                $user->activateBot($tariff->type);
                $user->notifyUser();

                ErrorLogHelper::logPaymentInfo('Успех оплаты Meleton');

                return 'ok';
            }
            ErrorLogHelper::logPaymentInfo(json_encode($model->errors), 'Ошибка оплаты Meleton');
        }

        \Yii::$app->response->statusCode = 400;
        ErrorLogHelper::logPaymentInfo(json_encode($data), 'Ошибка оплаты Meleton');

        return 'error';
    }
}