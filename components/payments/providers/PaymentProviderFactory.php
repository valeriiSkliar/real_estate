<?php

namespace app\components\payments\providers;

use app\components\payments\interfaces\PaymentProviderInterface;
use app\enums\PaymentProviders;
use app\helpers\ErrorLogHelper;
use Exception;
use yii\base\InvalidArgumentException;

class PaymentProviderFactory
{
    /**
     * Get the payment provider instance.
     *
     * @param string $provider The name of the provider.
     *
     * @return PaymentProviderInterface The payment provider instance.
     * @throws InvalidArgumentException|Exception if the provider is not supported.*@throws \Exception
     */
    public static function getProvider(string $provider): PaymentProviderInterface
    {
        try {
            return match (strtolower($provider)) {
                'meleton' => new MeletonPaymentProvider(),
                'lava' => new LavaPaymentProvider(),
                'manual' => new ManualPaymentProvider(),
                PaymentProviders::PAYPAL->value  => new PaypalPaymentProvider(),
                PaymentProviders::STRIPE->value => new StripePaymentProvider(),
                default => throw new InvalidArgumentException("Unsupported payment provider: {$provider}"),
            };
        } catch (Exception $e) {
            ErrorLogHelper::logPaymentInfo($e->getMessage(), 'Payment provider error');

            throw $e;
        }
    }
}