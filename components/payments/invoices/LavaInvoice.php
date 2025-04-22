<?php

namespace app\components\payments\invoices;

use app\enums\PaymentStatuses;
use app\helpers\ErrorLogHelper;
use app\models\BotUsers;
use app\models\Payments;
use app\models\Tariffs;
use Yii;

class LavaInvoice
{
    /**
     * @throws \JsonException
     */
    public function process(): string
    {
        $headers = Yii::$app->request->headers;
        $data = json_decode(Yii::$app->request->getRawBody(), true, 512, JSON_THROW_ON_ERROR);

        ErrorLogHelper::logPaymentInfo($headers, 'New Lava payment');
        ErrorLogHelper::logPaymentInfo(json_encode($data, JSON_THROW_ON_ERROR), 'Lava payment переданные данные');
        
        $secret = getenv('LAVA_SECRET');

        if (!isset($headers['X-Api-Key']) || $headers['X-Api-Key'] != $secret) {
            ErrorLogHelper::logPaymentInfo('wrong secret'. $headers['X-Api-Key'] ?? '', 'Неправильный X-Api-Key или секрет');

            return 'wrong secret';
        }

        $email = $data['buyer']['email'] ?? '';

        if (!$email){
            Yii::info('no email', 'payment');

            return 'no email';
        }

        if ($data['status'] !== 'completed'){
            ErrorLogHelper::logPaymentInfo('Lava вернула статус не completed, значит оплат не завершена для пользователя ' . $email);

            return 'status not completed';
        }

        $user = BotUsers::findOne(['payment_email' => $email]);

        if (!$user) {
            $parts = explode('@', $email);
            $userId = $parts[0] ?? null;

            $user = $userId && is_numeric($userId) ? BotUsers::findOne($userId) : null;
        }

        if (!$user){
            ErrorLogHelper::logPaymentInfo('no user for email '. $email );

            return 'no user';
        }

        $userId = $user->id;

        $tariffs = Tariffs::find()
            ->where([
                'price' => $data['amount'],
                'currency_code' => $data['currency'],
                'provider' => 'lava'
            ])
            ->all();

        if (!$tariffs){
            ErrorLogHelper::logPaymentInfo("Нет тарифа,по заданным параметрам({$data['amount']}|{$data['currency']}) для пользователя $userId");

            return 'no tariff';
        }

        $tariffIds = array_map(function($tariff) {
            return $tariff->id;
        }, $tariffs);

        $model = Payments::find()
            ->where([
                'user_id'    => $userId,
                'uuid'       => $data['contractId'] ?? 0,
                'status'     => PaymentStatuses::NEW->value,
                'tariff_id'  => $tariffIds,
            ])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(1)
            ->one();

        if (!$model) {
            //Уникальный идентификатор контракта не используем, т.к при закрытии окна платежки он меняется и у нас не совпадают они.
            $model = Payments::find()
                ->where([
                    'user_id'    => $userId,
                    'status'     => PaymentStatuses::NEW->value,
                    'tariff_id'  => $tariffIds,
                ])
                ->orderBy(['created_at' => SORT_DESC])
                ->limit(1)
                ->one();
        }

        if ($model) {
            $model->status = PaymentStatuses::SUCCESS->value;
            $tariff = Tariffs::findOne($model->tariff_id);

            if($model->save()) {
                $user->activateBot($tariff->type);
                $user->notifyUser();

                return 'ok';
            }
            ErrorLogHelper::logPaymentInfo(json_encode($model->errors, JSON_THROW_ON_ERROR), 'Ошибка сохранения платежа');

            return 'error';
        }else {
            ErrorLogHelper::logPaymentInfo('Нет платежа для пользователя '. $userId .' с tariff_id ' . json_encode($tariffIds));
        }

        Yii::$app->response->statusCode = 400;
        ErrorLogHelper::logPaymentInfo(json_encode($data, JSON_THROW_ON_ERROR), 'Ошибка оплаты Lava');

        return 'error';
    }
}