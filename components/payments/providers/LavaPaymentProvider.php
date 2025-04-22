<?php

namespace app\components\payments\providers;

use app\components\payments\interfaces\PaymentProviderInterface;
use app\helpers\ErrorLogHelper;
use app\models\BotUsers;
use app\models\Payments;
use app\models\Tariffs;
use GuzzleHttp\Client;
use Yii;

class LavaPaymentProvider implements PaymentProviderInterface
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function generatePaymentLink(BotUsers $user, Tariffs $tariff, Payments $payment): string
    {

        $language = Yii::$app->language === 'ru' ? 'RU' : 'EN';

        // Подготавливаем данные для запроса
        $data = [
            "email" => $user->payment_email ?: $user->id . '@example.com',
            "offerId" => $tariff->uuid,
            "currency" => $tariff->currency_code,
            "buyerLanguage" => $language,
        ];

        if ($tariff->bank_provider) {
           $data['paymentMethod'] = $tariff->bank_provider;
        }

        // Заголовки, необходимые для запроса
        $headers = [
            'X-Api-Key' => getenv('LAVA_API_KEY'),
            'Content-Type' => 'application/json',
        ];

        // Отправляем запрос с использованием Guzzle
        try {
            $response = $this->client->post($tariff->link, [
                'headers' => $headers,
                'json' => $data,
            ]);

            $responseData = json_decode($response->getBody(), true);

            if (!isset($responseData['paymentUrl'], $responseData['id'])) {
                ErrorLogHelper::logPaymentInfo(json_encode($responseData, JSON_THROW_ON_ERROR), 'Lava ответила ошибкой error');

                throw new \RuntimeException('Error while generating payment link');
            }

            ErrorLogHelper::logPaymentInfo(json_encode($responseData, JSON_THROW_ON_ERROR), 'Lava успех генерации платежной ссылки');

            // Обновляем UUID платежа
            $payment->updateAttributes(['uuid' => $responseData['id']]);

            // Возвращаем URL для оплаты
            return $responseData['paymentUrl'];
        } catch (\Exception $e) {
            ErrorLogHelper::logPaymentInfo('Request error: ' . $e->getMessage(), 'Lava ошибка при генерации платежной ссылки');

            throw new \RuntimeException('Error while generating payment link');
        }
    }
}