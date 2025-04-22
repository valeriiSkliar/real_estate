<?php

namespace app\components\payments\invoices;

use app\enums\PaymentStatuses;
use app\helpers\ErrorLogHelper;
use app\models\Payments;
use app\models\Tariffs;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use RuntimeException;

class PaypalInvoice
{
    private string $accessToken;
    private string $apiBase;
    private Client $client;

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function __construct()
    {
        $this->client = new Client();
        // Определяем среду - Sandbox или Live
        $useSandbox = (bool) getenv('PAYPAL_SANDBOX');
        $this->apiBase = $useSandbox ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';
        // Получаем access token для запроса
        $this->accessToken = $this->getAccessToken();
    }

    /**
     * @throws JsonException|Exception|GuzzleException
     * @throws InvalidConfigException
     */
    public function process(): string
    {
        $request = Yii::$app->request;
        $data = json_decode($request->getRawBody(), true, 512, JSON_THROW_ON_ERROR);
        ErrorLogHelper::logPaymentInfo(json_encode($data, JSON_THROW_ON_ERROR), 'PayPal webhook data');

        // Получаем заголовки
        $headers = $request->headers;

        $transmissionId = $headers->get('PayPal-Transmission-Id');
        $transmissionTime = $headers->get('PayPal-Transmission-Time');
        $certUrl = $headers->get('PayPal-Cert-Url');
        $authAlgo = $headers->get('PayPal-Auth-Algo');
        $transmissionSig = $headers->get('PayPal-Transmission-Sig');

        if (!$transmissionId || !$transmissionTime || !$certUrl || !$authAlgo || !$transmissionSig) {
            ErrorLogHelper::logPaymentInfo('Missing required PayPal verification headers', 'PayPal error');
            return 'error';
        }

        // Идентификатор вебхука
        $webhookId = getenv('PAYPAL_WEBHOOK_ID');
        if (!$webhookId) {
            ErrorLogHelper::logPaymentInfo('No PAYPAL_WEBHOOK_ID provided', 'PayPal error');
            return 'error';
        }

        // Проверяем подлинность вебхука
        if (!$this->verifyWebhookSignature($data, $transmissionId, $transmissionTime, $certUrl, $authAlgo, $transmissionSig, $webhookId)) {
            ErrorLogHelper::logPaymentInfo('Webhook signature verification failed', 'PayPal error');
            return 'error';
        }

        // Извлекаем order_id (uuid)
        $orderId = $data['resource']['id'] ?? null;
        if (!$orderId) {
            ErrorLogHelper::logPaymentInfo('No order id in webhook', 'PayPal error');
            return 'error';
        }

        $eventType = $data['event_type'] ?? '';
        $subscriptionId = $data['resource']['billing_agreement_id'] ?? null;

        switch ($eventType) {
            case 'CHECKOUT.ORDER.APPROVED':
            case'PAYMENT.CAPTURE.COMPLETED':
                return $this->processSinglePayment($orderId, $eventType);
            case 'PAYMENT.SALE.COMPLETED':
                return $this->handleSubscriptionActivated($subscriptionId, $eventType);
            default:
                ErrorLogHelper::logPaymentInfo('Webhook has unsupported event type: '. $eventType, 'PayPal error');

                throw new RuntimeException('Unsupported event type: '. $eventType);
        }
    }

    /**
     * @throws Exception
     * @throws JsonException
     * @throws InvalidConfigException
     */
    private function handleSubscriptionActivated(string $subscriptionId, $eventType): string
    {
        $paymentDetails = $this->getPaymentDetails($eventType);

        $total = $paymentDetails['total'] ?? 0;
        $commission = $paymentDetails['commission'] ?? 0;

        return $this->handleOrder(
            orderId: $subscriptionId,
            subscription: true,
            total: $total,
            commission: $commission
        );
    }

    /**
     * @throws Exception
     * @throws JsonException
     * @throws InvalidConfigException
     */
    private function processSinglePayment($orderId, $eventType): string
    {
        $captureData = null;

        if ($eventType === 'CHECKOUT.ORDER.APPROVED') {
            $captureData = $this->captureOrder($orderId);

            if (!$captureData) {
                ErrorLogHelper::logPaymentInfo('Захват платежа не произведен или завершён неуспешно', 'PayPal error');

                return 'error';
            }
        } elseif (in_array($eventType, ['PAYMENT.SALE.COMPLETED', 'PAYMENT.CAPTURE.COMPLETED'])) {
            $captureData = Yii::$app->request->getBodyParams()['resource'] ?? null;
        }

        if (!$captureData) {
            ErrorLogHelper::logPaymentInfo('Нет данных о платеже для обработки', 'PayPal error');

            return 'error';
        }

        // Извлекаем полную стоимость и комиссию
        $paymentDetails = $this->getPaymentDetails($eventType, $captureData);

        $total = $paymentDetails['total'];
        $commission = $paymentDetails['commission'];

        // Обрабатываем заказ
        return $this->handleOrder($orderId, false, $total, $commission);
    }

    /**
     * @throws Exception
     * @throws JsonException|InvalidConfigException
     */
    private function handleOrder(
        $orderId,
        $subscription = false,
        float $total = 0.0,
        float $commission = 0.0
    ): string {
        $oldStatus = PaymentStatuses::NEW->value;
        $newStatus = PaymentStatuses::SUCCESS->value;

        if ($subscription) {
            $oldStatus = [
                PaymentStatuses::NEW->value,
                PaymentStatuses::SUBSCRIPTION_ACTIVATED->value,
                PaymentStatuses::SUBSCRIPTION_RENEWED->value
            ];
            $newStatus = PaymentStatuses::SUBSCRIPTION_ACTIVATED->value;
        }

        // Находим платеж по uuid
        /** @var Payments $payment */
        $payment = Payments::find()
            ->where([
                'uuid' => $orderId,
                'status' => $oldStatus,
            ])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();

        if (!$payment) {
            ErrorLogHelper::logPaymentInfo("No payment for order_id: {$orderId}", 'PayPal error');
            return 'error';
        }

        if ($subscription){
            if ($payment->status === PaymentStatuses::SUBSCRIPTION_ACTIVATED->value) {
                $newStatus = PaymentStatuses::SUBSCRIPTION_RENEWED->value;
            }
        }

        $user = $payment->user;
        $tariff = Tariffs::findOne($payment->tariff_id);

        if (!$user || !$tariff) {
            ErrorLogHelper::logPaymentInfo('No user or tariff for payment ' . $payment->id, 'PayPal error');
            return 'error';
        }

        $payment->status = $newStatus;
        $payment->acknowledged = true;

        if ($payment->save()) {
            $user->activateBot($tariff->type, $total, $commission);
            $user->notifyUser();
            $payment->incrementPromoCodeCount();

            return 'ok';
        } else {
            ErrorLogHelper::logPaymentInfo(json_encode($payment->errors, JSON_THROW_ON_ERROR), 'Ошибка сохранения платежа');
            return 'error';
        }
    }

    /**
     * Проверка подписи вебхука.
     *
     * @param array $data Данные вебхука (resource)
     * @param string $transmissionId
     * @param string $transmissionTime
     * @param string $certUrl
     * @param string $authAlgo
     * @param string $transmissionSig
     * @param string $webhookId Идентификатор вебхука
     * @return bool
     * @throws JsonException
     * @throws GuzzleException
     */
    private function verifyWebhookSignature(array $data, string $transmissionId, string $transmissionTime, string $certUrl, string $authAlgo, string $transmissionSig, string $webhookId): bool
    {
        $verifyData = [
            'transmission_id' => $transmissionId,
            'transmission_time' => $transmissionTime,
            'cert_url' => $certUrl,
            'auth_algo' => $authAlgo,
            'transmission_sig' => $transmissionSig,
            'webhook_id' => $webhookId,
            'webhook_event' => $data,
        ];

        $response = $this->client->post($this->apiBase . '/v1/notifications/verify-webhook-signature', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$this->accessToken}",
            ],
            'json' => $verifyData
        ]);

        $body = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        if (!isset($body['verification_status'])) {
            ErrorLogHelper::logPaymentInfo('No verification_status in PayPal verification response', 'PayPal error');
            return false;
        }

        return $body['verification_status'] === 'SUCCESS';
    }

    /**
     * Получаем access_token для PayPal API.
     *
     * @return string
     * @throws GuzzleException
     * @throws JsonException
     */
    private function getAccessToken(): string
    {
        $clientId = getenv('PAYPAL_CLIENT_ID');
        $clientSecret = getenv('PAYPAL_CLIENT_SECRET');

        if (!$clientId || !$clientSecret) {
            throw new RuntimeException('PayPal CLIENT_ID or CLIENT_SECRET not set');
        }

        $auth = base64_encode("{$clientId}:{$clientSecret}");
        $response = $this->client->post("{$this->apiBase}/v1/oauth2/token", [
            'headers' => [
                'Authorization' => "Basic {$auth}",
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'grant_type' => 'client_credentials',
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        return $data['access_token'] ?? '';
    }

    /**
     * Принимает заказ и завершает его на PayPal.
     *
     * @param string $orderId
     * @return array|null
     */
    private function captureOrder(string $orderId): ?array
    {
        try {
            $response = $this->client->post("{$this->apiBase}/v2/checkout/orders/{$orderId}/capture", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer {$this->accessToken}",
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if ($data['status'] === 'COMPLETED') {
                return $data;
            }

            ErrorLogHelper::logPaymentInfo("Capture status not COMPLETED for order {$orderId}", 'PayPal error');
            return null;
        } catch (Throwable $e) {
            ErrorLogHelper::logPaymentInfo('Ошибка т.н. захвата платежа: ' . $e->getMessage(), 'PayPal error');

            return null;
        }
    }

    /**
     * Извлекает полную стоимость и комиссию из данных вебхука.
     *
     * @param array $resource Данные ресурса вебхука
     * @return array Ассоциативный массив с ключами 'total' и 'commission'
     * @throws InvalidConfigException
     */
    private function getPaymentDetails(string $eventType, array $resource = []): array
    {
        if (!$resource) {
            $resource = Yii::$app->request->getBodyParams()['resource'] ?? null;
        }

        $total = 0.0;
        $commission = 0.0;

        switch ($eventType) {
            case 'PAYMENT.SALE.COMPLETED':
            case 'PAYMENT.CAPTURE.COMPLETED':
                // Для событий платежей
                if (isset($resource['amount']['value'])) {
                    $total = (float) $resource['amount']['value'];
                }

                if (isset($resource['transaction_fee']['value'])) {
                    $commission = (float) $resource['transaction_fee']['value'];
                }

                break;

            case 'CHECKOUT.ORDER.APPROVED':
                // Для события одобрения заказа, данные захвата будут в captureOrder
                if (isset($resource['amount']['value'])) {
                    $total = (float) $resource['amount']['value'];
                }

                if (isset($resource['transaction_fee']['value'])) {
                    $commission = (float) $resource['transaction_fee']['value'];
                }

                break;

            case 'BILLING.SUBSCRIPTION.ACTIVATED':
            case 'BILLING.SUBSCRIPTION.RENEWED':
                // Для событий подписок
                if (isset($resource['billing_info']['last_payment']['amount']['value'])) {
                    $total = (float) $resource['billing_info']['last_payment']['amount']['value'];
                }

                break;

            default:
                ErrorLogHelper::logPaymentInfo("Unsupported event type for payment details extraction: {$eventType}", 'PayPal error');
                break;
        }

        return [
            'total' => $total,
            'commission' => $commission,
        ];
    }
}
