<?php

namespace app\components\payments\providers;

use app\components\payments\interfaces\PaymentProviderInterface;
use app\components\RouteBuilder;
use app\helpers\ErrorLogHelper;
use app\models\BotUsers;
use app\models\Payments;
use app\models\Tariffs;
use GuzzleHttp\Client;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Yii;

class PaypalPaymentProvider implements PaymentProviderInterface
{
    private Client $client;
    private string $clientId;
    private string $clientSecret;
    private string $apiBase;

    public function __construct()
    {
        // Определяем, что используем Sandbox или Live
        $useSandbox = (bool) getenv('PAYPAL_SANDBOX');

        $this->apiBase = $useSandbox ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';
        $this->clientId = getenv('PAYPAL_CLIENT_ID');
        $this->clientSecret = getenv('PAYPAL_CLIENT_SECRET');
        $this->client = new Client();
    }

    /**
     * @throws Exception|GuzzleException
     */
    public function generatePaymentLink(BotUsers $user, Tariffs $tariff, Payments $payment): string
    {
        if ($tariff->subscription_id) {
            return $this->generateSubscriptionLink($user, $tariff, $payment);
        }

        // Шаг 1: Получаем access_token
        $accessToken = $this->getAccessToken();

        // Шаг 2: Создаем платеж в PayPal
        $body = [
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => $tariff->currency_code ?: "USD",
                        "value" => (string) $tariff->price,
                    ],
                    "custom_id" => (string) $payment->id,
                ]
            ],
            "application_context" => [
                "cancel_url" => RouteBuilder::to(getenv('BOT_LINK')),
                "return_url" => RouteBuilder::to(getenv('BOT_LINK')),
            ]
        ];

        try {
            $response = $this->client->post("{$this->apiBase}/v2/checkout/orders", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer {$accessToken}"
                ],
                'json' => $body,
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (!isset($data['links'])) {
                throw new Exception('No links found in PayPal response');
            }

            $approvalUrl = null;
            foreach ($data['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    $approvalUrl = $link['href'];
                    break;
                }
            }

            if (!$approvalUrl) {
                throw new Exception('No approval_url found');
            }

            // Обновляем uuid платежа или сохраняем PayPal Order ID (id)
            if (isset($data['id'])) {
                $payment->updateAttributes(['uuid' => $data['id']]);
            }

            return $approvalUrl;

        } catch (Exception $e) {
            ErrorLogHelper::logPaymentInfo('PayPal request error: ' . $e->getMessage(), 'PayPal error');
            throw new \RuntimeException('Error generating PayPal payment link');
        }
    }

    public function generateSubscriptionLink(BotUsers $user, Tariffs $tariff, Payments $payment): string
    {
        $accessToken = $this->getAccessToken();

        $body = [
            "plan_id" => $tariff->subscription_id,
            "subscriber" => [
                "email_address" => $user->payment_email,
            ],
            "application_context" => [
                "cancel_url" => RouteBuilder::to(getenv('BOT_LINK')),
                "return_url" => RouteBuilder::to(getenv('BOT_LINK')),
            ]
        ];

        try {
            $response = $this->client->post("{$this->apiBase}/v1/billing/subscriptions", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer {$accessToken}"
                ],
                'json' => $body,
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (!isset($data['links'])) {
                throw new Exception('No links found in PayPal subscription response');
            }

            $approvalUrl = null;
            foreach ($data['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    $approvalUrl = $link['href'];
                    break;
                }
            }

            if (!$approvalUrl) {
                throw new Exception('No approval_url found for subscription');
            }

            // Сохраняем PayPal Subscription ID
            if (isset($data['id'])) {
                $payment->updateAttributes(['uuid' => $data['id']]);
            }

            return $approvalUrl;

        } catch (Exception $e) {
            ErrorLogHelper::logPaymentInfo('PayPal subscription request error: ' . $e->getMessage(), 'PayPal error');
            throw new \RuntimeException('Error generating PayPal subscription link');
        }
    }

    private function getAccessToken(): string
    {
        $auth = base64_encode("{$this->clientId}:{$this->clientSecret}");
        $response = $this->client->post("{$this->apiBase}/v1/oauth2/token", [
            'headers' => [
                'Authorization' => "Basic {$auth}",
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'form_params' => [
                'grant_type' => 'client_credentials'
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        return $data['access_token'] ?? '';
    }
}
