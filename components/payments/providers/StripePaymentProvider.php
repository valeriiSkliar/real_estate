<?php

namespace app\components\payments\providers;

use app\components\payments\interfaces\PaymentProviderInterface;
use app\components\RouteBuilder;
use app\helpers\ErrorLogHelper;
use app\models\BotUsers;
use app\models\Payments;
use app\models\Promocodes;
use app\models\Tariffs;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Exception;

class StripePaymentProvider implements PaymentProviderInterface
{
    private string $secretKey;
    private string $publishableKey;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->secretKey = getenv('STRIPE_SECRET_KEY');
        $this->publishableKey = getenv('STRIPE_PUBLISHABLE_KEY');

        if (!$this->secretKey || !$this->publishableKey) {
            throw new Exception('Stripe API keys are not set');
        }

        Stripe::setApiKey($this->secretKey);
    }

    /**
     * @throws Exception
     */
    public function generatePaymentLink(BotUsers $user, Tariffs $tariff, Payments $payment): string
    {
        try {
            $sessionParams = [
                'payment_method_types' => ['card'],
                'line_items' => $this->getLineItems($tariff),
                'mode' => $tariff->subscription_id ? 'subscription' : 'payment',
                'success_url' => RouteBuilder::to(getenv('BOT_LINK')),
                'cancel_url' => RouteBuilder::to(getenv('BOT_LINK')),
                'metadata' => [
                    'payment_id' => $payment->id,
                    'user_id' => $user->id,
                    'tariff_id' => $tariff->id,
                ],
            ];

            if ($tariff->subscription_id) {
                $sessionParams['subscription_data'] = [
                    'metadata' => [
                        'payment_id' => $payment->id,
                        'user_id' => $user->id,
                        'tariff_id' => $tariff->id,
                    ],
                ];
            }

            $promoCode = $payment->promo_code;

            if ($promoCode) {
                $promoModel = Promocodes::findOne(['code' => $promoCode]);

                if ($promoModel) {
                    $sessionParams['discounts'] = [[
                        'promotion_code' => $promoModel->payment_id,
                    ]];
                }
            }

            $session = Session::create($sessionParams);

            // Сохраняем Stripe Session ID
            $payment->updateAttributes(['uuid' => $session->id]);

            return $session->url;

        } catch (Exception $e) {
            ErrorLogHelper::logPaymentInfo('Stripe request error: ' . $e->getMessage(), 'Stripe error');
            throw new \RuntimeException('Error generating Stripe payment link');
        }
    }

    private function getLineItems(Tariffs $tariff): array
    {
        return $tariff->subscription_id
            ? [[
                'price' => $tariff->subscription_id,
                'quantity' => 1,
            ]]
            : [[
            'price_data' => [
                'currency' => strtolower($tariff->currency_code) ?: 'usd',
                'product_data' => [
                    'name' => $tariff->name,
                ],
                'unit_amount' => $tariff->price * 100, // Stripe принимает сумму в центах
            ],
            'quantity' => 1,
        ]];
    }
}
